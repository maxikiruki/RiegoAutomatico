

//LIBRERIA DEL RELOJ
#include "RTClib.h"
#include <Wire.h>
//LIBRERIA ETHERNET SHIELD
#include <SPI.h>
#include <Ethernet.h>
//LIBRERIA BASE DE DATOS
#include <MySQL_Connection.h>
#include <MySQL_Cursor.h>
#include <MySQL_Encrypt_Sha1.h>
#include <MySQL_Packet.h>
///////////////////TARJETA DE RED////////////////////
byte mac[] = {
  0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED
};
IPAddress ip(192, 168, 1, 200); // Dirección IP de la tarjeta de red
EthernetServer server(80);    // Puerto 80 por defecto para HTTP
/////////////////CONEXION MYSQL//////////////////////////////
IPAddress server_addr(192, 168, 1, 6); //IP del servidor
char user[] = "arduino";              //Usuario de la base de datos
char password[] = "Arduino123";       //Contraseña de la base de datos
///////////////////INICIALIZO////////////////////
EthernetClient client;
MySQL_Connection conn((Client *)&client);
//Creo el cursos para la conexion
MySQL_Cursor cur = MySQL_Cursor(&conn);
///////////////////RELOJ////////////////////

RTC_DS3231 rtc;
//////////////////ELEMENTOS///////////////////////
#define sensor A0
const int Rele_Valvula1 = 9;
const int Rele_Caudal1 = 25;
//CAUDAL
const int sensorPin = 2;
const int measureInterval = 2500;
volatile int pulseConter;

// YF-S201
const float factorK = 7.5;

float volume = 0;
long t0 = 0;
//////////////////VARIABLES///////////////////////
String daysOfTheWeek[7] = { "sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday" };



bool manual = false;
bool programmed = false;
bool monday = NULL;
bool tuesday = NULL;
bool wednesday = NULL;
bool thursday = NULL;
bool friday = NULL;
bool saturday = NULL;
bool sunday = NULL;
String start_time_morning = "";
char end_time_morning = "";
char start_time_afternoon = "";
char end_time_afternoon = "";
char Hour[3] = {'\0', '\0', '\0'};
char Mins[3] = {'\0', '\0', '\0'};
bool regar = false;

/////////////////////////////////////////////////////////////////////////////////////////////////
void ISRCountPulse()
{
  pulseConter++;
}

float GetFrequency()
{
  pulseConter = 0;

  interrupts();
  delay(measureInterval);
  noInterrupts();

  return (float)pulseConter * 1000 / measureInterval;
}

void SumVolume(float dV)
{
  volume += dV / 60 * (millis() - t0) / 1000.0;
  t0 = millis();
}
////////////////////////////////////////////////////////////////////////////////////////////////
void setup() {
  Serial.begin(9600);
  delay(1000);
  Ethernet.begin(mac, ip);    //inicializa la conexiÛn Ethernet y el servidor
  delay(2000);
  if (conn.connect(server_addr, 3306, user, password)) {
    delay(1000);
    // You would add your code here to run a query once on startup.
    Serial.println("Connection success.");
  } else {
    Serial.println("Connection failed.");
    conn.close();
  }
  // Fijar a fecha y hora específica. En el ejemplo, 21 de Enero de 2016 a las 03:00:00
  rtc.begin();
  /*
    rtc.adjust(DateTime(2020, 5, 15, 20, 06, 0));
  */
  //ELEMENTOS
  pinMode(sensor, INPUT);
  pinMode(Rele_Valvula1, OUTPUT);
  pinMode(Rele_Caudal1, OUTPUT);
  attachInterrupt(digitalPinToInterrupt(sensorPin), ISRCountPulse, RISING);
  t0 = millis();


}
////////////////////////////////////////////////////////////////////////////////////////////////


void loop() {
  Serial.println("inicio");
  row_values *row = NULL;
  row_values *row2 = NULL;
  DateTime now = rtc.now();


  String today = daysOfTheWeek[now.dayOfTheWeek()];

  //ELEMENTOS
  int valorHumedad = map(analogRead(sensor), 0, 1023, 100, 0);

  Serial.print("Humedad: ");
  Serial.print(valorHumedad);
  Serial.println("%");

  digitalWrite(Rele_Valvula1, HIGH);
  delay(5000);
  digitalWrite(Rele_Valvula1, LOW);
  delay(5000);

  //CAUDAL
  digitalWrite(Rele_Caudal1, HIGH);
  // obtener frecuencia en Hz
  float frequency = GetFrequency();

  // calcular caudal L/min
  float flow_Lmin = frequency / factorK;
  SumVolume(flow_Lmin);

  Serial.print(" Caudal: ");
  Serial.print(flow_Lmin, 3);
  Serial.print(" (L/min)\tConsumo:");
  Serial.print(volume, 1);
  Serial.println(" (L)");
digitalWrite(Rele_Caudal1, LOW);
  /////////////////COMPRUEBO EL HORARIO DEL SECTOR 1 //////////////////////////////
  // Inicializo la isntancia que ejecuta las querys
  MySQL_Cursor *cur_mem = new MySQL_Cursor(&conn);
  // Ejecuto la consulta donde busco todos los datos del horario que tiene asociado el sector 1
  cur_mem->execute("SELECT schedule.start_time_morning, schedule.end_time_morning, schedule.start_time_afternoon, schedule.end_time_afternoon, schedule.monday, schedule.tuesday, schedule.wednesday, schedule.thursday, schedule.friday, schedule.saturday, schedule.sunday FROM riegoautomatico.schedule INNER JOIN riegoautomatico.sector ON schedule.id = sector.schedule_id WHERE sector.id = 1;");

  //Espero 2 segundos que se ejecute la consulta
  delay(2000);
  // Fetch the columns and print them
  column_names *cols = cur_mem->get_columns();
  row = cur_mem->get_next_row();
  if (row != NULL) {
    start_time_morning = row->values[0];
    end_time_morning = row->values[1];

    start_time_afternoon = row->values[2];
    end_time_afternoon = row->values[3];

    monday = atol(row->values[4]);
    tuesday = atol(row->values[5]);
    wednesday = atol(row->values[6]);
    thursday = atol(row->values[7]);
    friday = atol(row->values[8]);
    saturday = atol(row->values[9]);
    sunday = atol(row->values[10]);
  }

  /////////////////ELIMINO EL CURSOR//////////////////////////////
  delete cur_mem;
  Serial.println();

  //SEPARAR LA HORA LOS MINUTOS DE LA BASE DE DATOS Y GUARDARLO EN INT

  Hour[0] = start_time_morning[0];
  Hour[1] = start_time_morning[1];
  Mins[0] = start_time_morning[3];
  Mins[1] = start_time_morning[4];

  Serial.println("Primera consulta realizada");


  /////////////////COMPRUEBO SI EL SECTOR 1 ESTA PROGRAMADO Y/O MANUAL //////////////////////////////
  // Inicializo la instancia que ejecuta las querys
  MySQL_Cursor *cur_mem2 = new MySQL_Cursor(&conn);
  // Ejecuto la consulta donde busco todos los campos del estado del Sector 1
  cur_mem2->execute("SELECT on_off, programmed FROM riegoautomatico.state WHERE sector_id=1;");

  //Espero 2 segundos que se ejecute la consulta
  delay(2000);

  // Fetch the columns and print them
  column_names *cols2 = cur_mem2->get_columns();

  row2 = cur_mem2->get_next_row();

  manual = atol(row2->values[0]);

  programmed = atol(row2->values[1]);

  delay(1000);
  Serial.println("Programado");
  Serial.println(programmed);
  delay(1000);
  Serial.println("Manual");
  Serial.println(manual);


  /////////////////ELIMINO EL CURSOR//////////////////////////////
  delete cur_mem2;


  //COMPARO SI hoy es dia de riego
  if (today ==  "monday" && monday )
    regar = true;
  else if (today ==  "tuesday" && tuesday )
    regar = true;
  else if (today ==  "wednesday" && wednesday)
    regar = true;
  else if (today ==  "thursday" && thursday )
    regar = true;
  else if (today ==  "friday" && friday)
    regar = true;
  else if (today ==  "saturday" && saturday )
    regar = true;
  else if (today ==  "sunday" && sunday)
    regar = true;
  //FORMATEO LAS HORAS Y MINUTOS PARA PODER COMPARARLAS
  Serial.println();
  byte Hour_now = now.hour();
  byte Min_now = now.minute();

  String horaa = String(Hour[0]) + String(Hour[1]);
  int Hora_inicio_m = String(horaa).toInt();
  String minutoo = String(Mins[0]) + String(Mins[1]);
  int Minuto_inicio_m = String(minutoo).toInt();

  String horaa_actual = String(Hour_now);
  int Hora_now = String(horaa_actual).toInt();
  String minn_actual = String(Min_now);
  int Minu_now = String(minn_actual).toInt();
  Serial.println("ACTUAL");
  Serial.print(Hora_now);
  Serial.println(Minu_now);
  Serial.println("BASE DE DATOS");
  Serial.print(Hora_inicio_m);
  Serial.println(Minuto_inicio_m);
  delay(1000);

  //COMPARO SI LA HORA ACTUAL Y EL MINUTO ACTUAL COINCIDE CON LA BASE DE DATOS || MANUAL
  if ( (programmed = true) && (Hora_now == Hora_inicio_m) && (Minu_now == Minuto_inicio_m) && (regar = true ) || (manual = true)) {
    Serial.println("entra ");

  }


}
