


//LIBRERIA DEL RELOJ
  #include <DS1302.h>

//LIBRERIA ETHERNET SHIELD

  #include <SPI.h>
  #include <Ethernet.h>

//LIBRERIA BASE DE DATOS
  #include <MySQL_Connection.h>
  #include <MySQL_Cursor.h>
  #include <MySQL_Encrypt_Sha1.h>
  #include <MySQL_Packet.h>



//LIBRERIA FIRMATA
  #include <Servo.h>
  #include <Wire.h>
  #include <Firmata.h>

  #define I2C_WRITE                   B00000000
  #define I2C_READ                    B00001000
  #define I2C_READ_CONTINUOUSLY       B00010000
  #define I2C_STOP_READING            B00011000
  #define I2C_READ_WRITE_MODE_MASK    B00011000
  #define I2C_10BIT_ADDRESS_MODE_MASK B00100000
  #define I2C_END_TX_MASK             B01000000
  #define I2C_STOP_TX                 1
  #define I2C_RESTART_TX              0
  #define I2C_MAX_QUERIES             8
  #define I2C_REGISTER_NOT_SPECIFIED  -1


//CONF INTERNET
                               // Introduzca una direcciÛn MAC y la direcciÛn IP para el controlador
  byte mac[] = { 
  0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
  IPAddress ip(192,168,1,200);   // Dirección IP de la tarjeta de red
  EthernetServer server(80);    // Puerto 80 por defecto para HTTP

//CONF BASE DE DATOS

  IPAddress server_addr(192,168,1,6);  //IP del servidor
  char user[] = "arduino";              //Usuario de la base de datos
  char password[] = "Arduino123";       //Contraseña de la base de datos

//RESTO DE CONFIGURACION

  EthernetClient client;
  MySQL_Connection conn((Client *)&client);
  //Creo el cursos para la conexion
  MySQL_Cursor cur = MySQL_Cursor(&conn);

//CONFIGURO EL RELOJ
  DS1302  rtc (2 ,  3 ,  4);
  String dia = "";
  String day= rtc.getDOWStr();

 /* 
//INSTRUMENTOS DEL SECTOR 1
  //Valvula
  #define Valvula1 10; 
  //Sensor Humedad
  #define Humedad1 A0;  
  //Caudal de Agua
  #define Caudal1 A1;    
  //variables para querys
  int id_horarioSector1;
*/

void setup() {
  Serial.begin(9600);
  Ethernet.begin(mac, ip);    //inicializa la conexiÛn Ethernet y el servidor
  if (conn.connect(server_addr, 3306, user, password)) {
    delay(1000);
    // You would add your code here to run a query once on startup.
    Serial.println("Connection success.");
  }else{
    Serial.println("Connection failed.");
  conn.close();
  }
  
  
  // Establece RTC en modo de ejecución y desactiva la protección contra escritura 
    rtc.halt(false ); 
    rtc.writeProtect ( false );
  // Establezco el reloj en Jueves 16/04/2020 20:00
    /*
    rtc.setDOW(WEDNESDAY);
    rtc.setTime(20,8,00);
    rtc.setDate(22, 4, 2020);
    */
    /*
    if( day == "Monday" ){
    dia="Lunes";
  }else if(day == "Tuesday" ){
    dia="Martes";
  }else if(day == "Wednesday" ){
    dia="Miercoles";
  }else if(day == "Thursday" ){
    dia="Jueves";
  }else if(day == "Friday" ){
    dia="Viernes";
  }else if(day == "Saturday" ){
    dia="Sabado";
  }else if(day == "Sunday" ){
    dia="Domingo";
  }
 */

  
}
void loop() {
  /*
  Serial.print(rtc.getDOWStr());
  Serial.println(day);
  Serial.println(dia);
  */
  row_values *row = NULL;
  long head_count = 0;
  
  // Inicializo la isntancia que ejecuta las querys
  MySQL_Cursor *cur_mem = new MySQL_Cursor(&conn);
  
  // Ejecuto la consulta donde busco el id del horario que tiene asignado el Sector 1
  cur_mem->execute("SELECT schedule_id FROM riegoautomatico.sector WHERE id=1;");
  // Asocio las columnas, pero no lo voy a usar
  column_names *columns = cur_mem->get_columns();
  // Guardo el id de horario que teiene asignado el sector 1
  do {
    row = cur_mem->get_next_row();
    
    if (row != NULL) {
      head_count = atol(row->values[0]);
      Serial.println("entra");
    }
  } while (row != NULL);
  delete cur_mem;
 
  Serial.println(head_count);
  //Serial.println(row);
  
  

 
  //SELECT * FROM state WHERE id="id_horario"; =>fetch_assoc()
  //if (on_off= 1) => man=true;
  //if (programmed= 1) 
  //SELECT * FROM schedule WHERE id ="horario"; => fetch_assoc()
  // Elimino el cursor para que no me ocupe memoria
  
  //if( (dia == 1 && (rtc.getTimeStr()= start_time_morning) || (rtc.getTimeStr()= start_time_afternoon)) || man=true )
  //leo Humedad1_inicio
  //abro valvula
  //endif
  
  
 


  
}
