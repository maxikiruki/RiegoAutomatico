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

//Seteo los parametros de la tarjeta de red
byte mac[] = {
  0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED
};
IPAddress ip(192, 168, 1, 200); // Dirección IP de la tarjeta de red
EthernetServer server(80);    // Puerto 80 por defecto para HTTP
//Seteo los ajustes de la base de datos
IPAddress server_addr(192, 168, 1, 6); //IP del servidor
char user[] = "arduino";              //Usuario de la base de datos
char password[] = "Arduino123";       //Contraseña de la base de datos

EthernetClient client;
MySQL_Connection conn((Client *)&client);
//Creo el cursor para la conexion
MySQL_Cursor cur = MySQL_Cursor(&conn);
//Creo el reloj
RTC_DS3231 rtc;

//Elementos del Sector 1
#define Humedity1 A0
const int Relay_Valve1 = 9;
const int Relay_Flow1 = 25;
const int Flow1 = 2;
int Humedad_init = NULL;
int Humedad_final = NULL;
//Variables para calcular el caudal
const int measureInterval = 2500;
volatile int pulseConter;
// YF-S201
const float factorK = 7.5;
float volume = 0;
long t0 = 0;
float frequency = 0;

//Seteo de variables iniciales
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
String end_time_morning = "";
String start_time_afternoon = "";
String end_time_afternoon = "";
char Hour[3] = {'\0', '\0', '\0'};
char Mins[3] = {'\0', '\0', '\0'};
char Hour2[3] = {'\0', '\0', '\0'};
char Mins2[3] = {'\0', '\0', '\0'};
char Hour3[3] = {'\0', '\0', '\0'};
char Mins3[3] = {'\0', '\0', '\0'};
char Hour4[3] = {'\0', '\0', '\0'};
char Mins4[3] = {'\0', '\0', '\0'};
int Hora_inicio_m = NULL;
int Hora_final_m = NULL;
int Minuto_inicio_m = NULL;
int Minuto_final_m = NULL;

int Hora_inicio_a = NULL;
int Hora_final_a = NULL;
int Minuto_inicio_a = NULL;
int Minuto_final_a = NULL;


bool abrir = false;
bool regar = false;
bool hoy_regar = false;
bool regado = false;
float TotalLitros = NULL;

//Valores para el insert
String fecha = "";
String time_init = "";
String time_final = "";


//Consultas
char state_sector1[] = "SELECT on_off, programmed FROM riegoautomatico.state WHERE sector_id=1";
char schedule_sector1[] = "SELECT schedule.start_time_morning, schedule.end_time_morning, schedule.start_time_afternoon, schedule.end_time_afternoon, schedule.monday, schedule.tuesday, schedule.wednesday, schedule.thursday, schedule.friday, schedule.saturday, schedule.sunday FROM riegoautomatico.schedule INNER JOIN riegoautomatico.sector ON schedule.id = sector.schedule_id WHERE sector.id = 1;";
char insert_sector1[] = "";

//Funciones varias
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

  return (float)pulseConter * 1000 / measureInterval ;

}
void SumVolume(float dV)
{
  volume += dV / 60 * (millis() - t0) / 1000.0;
  t0 = millis();
}

void setup() {
  Serial.begin(9600);
  delay(5000);
  Ethernet.begin(mac, ip);
  delay(5000);
  if (conn.connect(server_addr, 3306, user, password)) {
    delay(2000);
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
  pinMode(Humedity1, INPUT);
  pinMode(Relay_Valve1, OUTPUT);
  pinMode(Relay_Flow1, OUTPUT);
  attachInterrupt(digitalPinToInterrupt(Flow1), ISRCountPulse, RISING);
  t0 = millis();
}
void loop() {
  Serial.println("inicio");
  //Obtengo la hora actual
  DateTime now = rtc.now();
  byte Hour_now = now.hour();
  byte Min_now = now.minute();
  String horaa_actual = String(Hour_now);
  int Hora_now = String(horaa_actual).toInt();
  String minn_actual = String(Min_now);
  int Minu_now = String(minn_actual).toInt();
  String today = daysOfTheWeek[now.dayOfTheWeek()];




  //Compruebo el estado del sector 1
  row_values *row = NULL;
  row = NULL;
  MySQL_Cursor *cur_mem = new MySQL_Cursor(&conn);
  cur_mem->execute(state_sector1);
  delay(2000);

  column_names *cols = cur_mem->get_columns();
  delay(2000);
  do {
    row = cur_mem->get_next_row();
    if (row != NULL) {
      manual = atol(row->values[0]);
      programmed = atol(row->values[1]);
    }
  } while (row != NULL);
  // Deleting the cursor also frees up memory used
  delete cur_mem;

  Serial.println(manual);
  Serial.println(programmed);

  delay(500);

  if (programmed) {
    Serial.println("Entra en programmed");
    row_values *row2 = NULL;
    row2 = NULL;
    //Consulto si hoy es dia de riego
    MySQL_Cursor *cur_mem2 = new MySQL_Cursor(&conn);
    cur_mem2->execute(schedule_sector1);
    delay(2000);

    column_names *cols2 = cur_mem2->get_columns();
    delay(2000);
    do {
      row2 = cur_mem2->get_next_row();
      if (row2 != NULL) {
        Serial.println("Entra if row != null  linea 213");
        start_time_morning = row2->values[0];
        end_time_morning = row2->values[1];

        start_time_afternoon = row2->values[2];
        end_time_afternoon = row2->values[3];

        monday = atol(row2->values[4]);
        tuesday = atol(row2->values[5]);
        wednesday = atol(row2->values[6]);
        thursday = atol(row2->values[7]);
        friday = atol(row2->values[8]);
        saturday = atol(row2->values[9]);
        sunday = atol(row2->values[10]);
      }
    } while (row2 != NULL);
    // Deleting the cursor also frees up memory used
    delete cur_mem2;


    //Formateo la hora de inicio y final de la mañana
    //HORA INICIO MAÑANA
    Hour[0] = start_time_morning[0];
    Hour[1] = start_time_morning[1];
    Mins[0] = start_time_morning[3];
    Mins[1] = start_time_morning[4];
    String horaa = String(Hour[0]) + String(Hour[1]);
    Hora_inicio_m = String(horaa).toInt();
    String minutoo = String(Mins[0]) + String(Mins[1]);
    Minuto_inicio_m = String(minutoo).toInt();

    //HORA FIN MAÑANA
    Hour2[0] = end_time_morning[0];
    Hour2[1] = end_time_morning[1];
    Mins2[0] = end_time_morning[3];
    Mins2[1] = end_time_morning[4];
    String horaa2 = String(Hour2[0]) + String(Hour2[1]);
    Hora_final_m = String(horaa2).toInt();
    String minutoo2 = String(Mins2[0]) + String(Mins2[1]);
    Minuto_final_m = String(minutoo2).toInt();


    //Formateo la hora de inicio y final de la tarde
    //HORA INICIO TARDE
    Hour3[0] = start_time_afternoon[0];
    Hour3[1] = start_time_afternoon[1];
    Mins3[0] = start_time_afternoon[3];
    Mins3[1] = start_time_afternoon[4];
    String horaa3 = String(Hour3[0]) + String(Hour3[1]);
    Hora_inicio_a = String(horaa3).toInt();
    String minutoo3 = String(Mins3[0]) + String(Mins3[1]);
    Minuto_inicio_a = String(minutoo3).toInt();

    //HORA FIN TARDE
    Hour4[0] = end_time_afternoon[0];
    Hour4[1] = end_time_afternoon[1];
    Mins4[0] = end_time_afternoon[3];
    Mins4[1] = end_time_afternoon[4];
    String horaa4 = String(Hour4[0]) + String(Hour4[1]);
    Hora_final_a = String(horaa4).toInt();
    String minutoo4 = String(Mins4[0]) + String(Mins4[1]);
    Minuto_final_a = String(minutoo4).toInt();


    //Compruebo si hoy es dia de riego, y si es hora de riego
    hoy_regar = false;
    //COMPARO SI hoy es dia de riego
    if (today ==  "monday" && monday )
      hoy_regar = true;
    else if (today ==  "tuesday" && tuesday )
      hoy_regar = true;
    else if (today ==  "wednesday" && wednesday)
      hoy_regar = true;
    else if (today ==  "thursday" && thursday )
      hoy_regar = true;
    else if (today ==  "friday" && friday)
      hoy_regar = true;
    else if (today ==  "saturday" && saturday )
      hoy_regar = true;
    else if (today ==  "sunday" && sunday)
      hoy_regar = true;

    if (  ((Hora_now == Hora_inicio_m) && (Minu_now == Minuto_inicio_m) && hoy_regar ) || ( (Hora_now == Hora_inicio_a) && (Minu_now == Minuto_inicio_a) && hoy_regar )) {
      regar = true;
    } else if (hoy_regar && (Hora_now == Hora_final_m && Minu_now == Minuto_final_m) || (Hora_now == Hora_final_a && Minu_now == Minuto_final_a)) {
      regar = false;
    }


    //SI programmed vale 0
  } else if (!programmed && manual ) {
    regar = true;
  } else if (!programmed && !manual) {
    regar = false;
  } else if (programmed && manual) {
    regar = true;
  }
  Serial.println("Regar");
  Serial.println(regar);
  if (regar && !regado ) {
    Serial.println("Comienza el riego");
    Serial.println("Abro elementos");
    digitalWrite(Relay_Valve1, HIGH);
    digitalWrite(Relay_Flow1, HIGH);
    //Obtengo la hora actual
    String hora_inicial = now.hour() + "";
    String minuto_inicial = now.minute() + "";
    time_init = hora_inicial + ":" + minuto_inicial;
    //Guardo la humedad inicial antes de accionar elementos
    Humedad_init = map(analogRead(Humedity1), 0, 1023, 100, 0);

    regado = true;
    Serial.println("Antes de entrar if regar && regado  linea 324");

  }
  Serial.println("REGADO");
  Serial.println(regado);
  if (regado) {
    int contador = 0;
    do {
      // obtener frecuencia en Hz
      float frequency = GetFrequency();
      if (contador = 20) {
        interrupts();
      }
      // calcular caudal L/min
      float flow_Lmin = frequency / factorK;
      SumVolume(flow_Lmin);

      Serial.print(" Caudal: ");
      Serial.print(flow_Lmin, 3);
      Serial.print(" (L/min)\tConsumo:");
      Serial.print(volume, 1);
      Serial.println(" (L)");
      contador = contador + 1;
      TotalLitros = volume;

    } while (regado && contador < 20);
  }

  if (regado &&  !regar) {
    Serial.println("Finaliza el riego");
    //Registro la humedad final
    Humedad_final = map(analogRead(Humedity1), 0, 1023, 100, 0);
    //Obtener la fecha final
    String anyo = now.year() + "";
    String mes = now.month() + "";
    String dia = now.day() + "";
    fecha = anyo + "-" + mes + "-" + dia ;
    String hora_final = now.hour() + "";
    String minuto_final = now.minute() + "";
    time_final = hora_final + ":" + minuto_final;
    //Apagado todo
    digitalWrite(Relay_Valve1, LOW);
    digitalWrite(Relay_Flow1, LOW);
    //Desactivo regar y regado
    regar = false;
    regado = false;




    //Los datos a insertar son
    //sector_id=1 date=fecha start_time=time_init end_time=time_final start_humedity=Humedad_init final_humedity=Humedad_final total_liters=TotalLitros

    //Hago el insert
    Serial.println("Preparando inserción en la base de datos");
    //INSERT INTO `history` (`id`, `sector_id`, `date`, `start_time`, `end_time`, `start_humidity`, `final_humidity`, `total_liters`)
    //VALUES (NULL, '1', '2020-05-24', '18:24:00', '18:30:00', '10', '75', '80.8');
    //String insert_sector01="INSERT INTO history.riegoautomatico (id, sector_id, date,start_time,end_time,start_humidity,final_humidity,total_liters) VALUES  (NULL,'1','"+fecha+"','"+time_init+"','"+time_final+"',"+Humedad_init+","+Humedad_final+","+TotalLitros+")";

    char insert_sector1[] = "INSERT INTO riegoautomatico.history (id,sector_id, date,start_time,end_time,start_humidity,final_humidity,total_liters) VALUES  (NULL,'1','%s','%s','%s',%d,%d,%f)";
    char query[128];
    sprintf(query, insert_sector1, fecha, time_init, time_final, Humedad_init, Humedad_final, TotalLitros);

    MySQL_Cursor *cur_mem3 = new MySQL_Cursor(&conn);
    cur_mem3->execute(query);
    

    if (cur_mem3->execute(query))
      Serial.println("Guardado");
    else
      Serial.println("Error al guardar");

    delete cur_mem3;

  }




}
