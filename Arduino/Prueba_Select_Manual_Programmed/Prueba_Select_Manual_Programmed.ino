
//LIBRERIA DEL RELOJ
#include "RTClib.h"

#include <SPI.h>
#include <Ethernet.h>
//LIBRERIA BASE DE DATOS
#include <MySQL_Connection.h>
#include <MySQL_Cursor.h>
#include <MySQL_Encrypt_Sha1.h>
#include <MySQL_Packet.h>
byte mac[] = {
  0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED
};
IPAddress ip(192, 168, 1, 200); // Dirección IP de la tarjeta de red
EthernetServer server(80);    // Puerto 80 por defecto para HTTP

IPAddress server_addr(192, 168, 1, 6); //IP del servidor
char user[] = "arduino";              //Usuario de la base de datos
char password[] = "Arduino123";       //Contraseña de la base de datos

bool manual = false;
bool programmed = false;

EthernetClient client;
MySQL_Connection conn((Client *)&client);
//Creo el cursos para la conexion
MySQL_Cursor cur = MySQL_Cursor(&conn);

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
/////////////////////////////////////////////////////////////////////////////////////////////////
void loop() {
  row_values *row = NULL;
  /*
    bool manual = 0;
    bool programmed = 0;

    Serial.println(manual);
    Serial.println(programmed);
  */
  Serial.println();
  Serial.println("Inicio");

  /////////////////COMPRUEBO SI EL SECTOR 1 ESTA PROGRAMADO Y/O MANUAL //////////////////////////////
  // Inicializo la instancia que ejecuta las querys
  MySQL_Cursor *cur_mem = new MySQL_Cursor(&conn);
  // Ejecuto la consulta donde busco todos los campos del estado del Sector 1
  cur_mem->execute("SELECT on_off, programmed FROM riegoautomatico.state WHERE sector_id=1;");

  //Espero 2 segundos que se ejecute la consulta
  delay(2000);

  // Fetch the columns and print them
  column_names *cols = cur_mem->get_columns();


  do {
    row = cur_mem->get_next_row();
    if (row != NULL) {
      for (int f = 0; f < cols->num_fields; f++) {
        delay(500);
        manual = atol(row->values[0]);
        programmed = atol(row->values[1]);
        /*
        if (f == 0) {
          manual = (row->values[f]);
          Serial.println("Manual entra");
          Serial.println(row->values[f]);
          delay(1000);
          
        } else if (f == 1) {
          programmed = (row->values[f]);
          Serial.println("Pogramado entra");
          Serial.println(row->values[f]);
          
          delay(1000);
        }
        */
      }
      Serial.println();
    }
  } while (row != NULL);
/*
  
    row = cur_mem->get_next_row();

    if (row->values[0] == 0) {
    manual = 0;
    } else {
    manual = 1;
    }
    if (row->values[1] == 0) {
    programmed = 0;
    } else {
    programmed = 1;
    }
  */

  Serial.println("Manual");
  Serial.println(manual);

  Serial.println("Programado");
  Serial.println(programmed);


  //cur_mem->close();
}
