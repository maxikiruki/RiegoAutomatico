
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


/////////////////////////////////////////////////////////////////////////////////////////////////
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

}
////////////////////////////////////////////////////////////////////////////////////////////////

void loop() {
  Serial.println("inicio");
  row_values *row = NULL;
  delay(2000);
  /////////////////COMPRUEBO EL HORARIO DEL SECTOR 1 //////////////////////////////
  // Inicializo la isntancia que ejecuta las querys
  MySQL_Cursor *cur_mem = new MySQL_Cursor(&conn);
  // Ejecuto la consulta donde busco todos los datos del horario que tiene asociado el sector 1
  cur_mem->execute("SELECT schedule.start_time_morning, schedule.end_time_morning, schedule.start_time_afternoon, schedule.end_time_afternoon, schedule.monday, schedule.tuesday, schedule.wednesday, schedule.thursday, schedule.friday, schedule.saturday, schedule.sunday FROM riegoautomatico.schedule INNER JOIN riegoautomatico.sector ON schedule.id = sector.schedule_id WHERE sector.id = 1;");

  //Espero 2 segundos que se ejecute la consulta
  delay(2000);
  // Fetch the columns and print them
  column_names *cols = cur_mem->get_columns();
  delay(2000);
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
  delay(2000);
  Serial.println("Lunes");
  Serial.println(monday);
  delay(2000);
  Serial.println("Martes");
  Serial.println(tuesday);







}
