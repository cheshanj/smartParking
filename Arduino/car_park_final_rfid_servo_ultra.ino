/*
*****IMPORTANT*****
----------------------------------------------------------------------------------
1. Before execute define all the pins if you have changed them.
2.check the ip of yours and update with the gatway ip and the sever ip.
3.if use different port for servo ,change it before executing.
4.update with correct pin layout of RFID reader.
5.when run the whole project,if you are facing with reading 
  free slots on raspberry,
  comment all serial.print in the arduino sketch 
  except the free slot serial.print.
----------------------------------------------------------------------------------
******Cheshan Jayathilaka |  IT15030436 | 27045224 | SHU final year project******
*/

#include <MySQL_Connection.h>
#include <MySQL_Cursor.h>
#include <MySQL_Encrypt_Sha1.h>
#include <MySQL_Packet.h>
#include <SPI.h>
#include <Ethernet.h>
#include <MFRC522.h>
#include <Servo.h>

#define echoPin1 2 // Echo Pin for sonar 1
#define trigPin1 3 // Trigger Pin for sonar 1
#define echoPin2 4 // Echo Pin for sonar 2 
#define trigPin2 5 // Trigger Pin for sonar 2
#define echoPin3 6 // Echo Pin for sonar 3
#define trigPin3 7 // Trigger Pin for sonar 3

#define SERVO_PIN 8
Servo myservo;  // create servo object to control a servo

#define SS_PIN 10
#define RST_PIN 9
MFRC522 mfrc522(SS_PIN, RST_PIN);   // Create MFRC522 instance.

byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };   //physical mac address
byte ip[] = { 192, 168, 0, 103 };                   // ip in lan
byte subnet[] = { 255, 255, 255, 0 };              //subnet mask
byte gateway[] = { 192, 168, 0, 1 };              // default gateway
IPAddress server(192, 168, 0, 100);


long duration_sensor_one, distance_sensor_one; // Duration used to calculate distance
long duration_sensor_two, distance_sensor_two;
long duration_sensor_three, distance_sensor_three;
int count = 0;
int freeSlot = 0;
String SensorID;
String slotStatus;

EthernetClient client;
MySQL_Connection conn((Client *)&client);
// Create an instance of the cursor passing in the connection
MySQL_Cursor cur = MySQL_Cursor(&conn);

void setup() {
  // Serial.begin starts the serial connection between computer and Arduino
  Serial.begin(9600);
  // start the Ethernet connection
  Ethernet.begin(mac, ip, gateway, subnet);
  SPI.begin();
  mfrc522.PCD_Init();

  myservo.attach(SERVO_PIN);
  myservo.write( 0 );

  pinMode(trigPin1, OUTPUT); // trigger pin as output
  pinMode(echoPin1, INPUT);  // echo pin as input
  pinMode(trigPin2, OUTPUT);
  pinMode(echoPin2, INPUT);
  pinMode(trigPin3, OUTPUT);
  pinMode(echoPin3, INPUT);
}

void loop() {

  digitalWrite(trigPin1, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin1, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin1, LOW);
  // pulseIn( ) function determines a pulse width in time
  // duration of pulse is proportional to distance of obstacle
  duration_sensor_one = pulseIn(echoPin1, HIGH);

  digitalWrite(trigPin2, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin2, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin2, LOW);
  duration_sensor_two = pulseIn(echoPin2, HIGH);

  digitalWrite(trigPin3, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin3, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin3, LOW);
  duration_sensor_three = pulseIn(echoPin3, HIGH);

  distance_sensor_one = duration_sensor_one / 74 / 2; //Calculating distance value to Inches.
  if (distance_sensor_one < 12) { //If ground clearence of the vehicle lower than 12,then only database update with the sensor data.
    distance_sensor_one = 1;  //store the status that is the sensor is trggered due to the slot is in use.
    SensorID = "Ultra_sensor_01";
    slotStatus = "Occupied";
    dataSending_OccupiedSlot(SensorID, slotStatus);
  }
  else
  {
    distance_sensor_one = 0;
    SensorID = "Ultra_sensor_01";
    slotStatus = "Free";
    dataSending_OccupiedSlot(SensorID, slotStatus);
  }

  distance_sensor_two = duration_sensor_two / 74 / 2;
  if (distance_sensor_two < 12) {
    distance_sensor_two = 1;
    SensorID = "Ultra_sensor_02";
    slotStatus = "Occupied";
    dataSending_OccupiedSlot(SensorID, slotStatus);
  }
  else {
    distance_sensor_two = 0;
    SensorID = "Ultra_sensor_02";
    slotStatus = "Free";
    dataSending_OccupiedSlot(SensorID, slotStatus);
  }

  distance_sensor_three = duration_sensor_three / 74 / 2;
  if (distance_sensor_three < 12) {
    distance_sensor_three = 1;
    SensorID = "Ultra_sensor_03";
    slotStatus = "Occupied";
    dataSending_OccupiedSlot(SensorID, slotStatus);
  }
  else {
    distance_sensor_three = 0;
    SensorID = "Ultra_sensor_03";
    slotStatus = "Free";
    dataSending_OccupiedSlot(SensorID, slotStatus);
  }
  count = distance_sensor_one + distance_sensor_two + distance_sensor_three ;

  // free slot = total slot - total car
  freeSlot = 3 - count;
  // number of total slot is sent to raspberry pi using usb
  Serial.println(freeSlot);
  // the status is updated every 30 seconds.
  delay(5000);

  //Look for new cards
  if ( !mfrc522.PICC_IsNewCardPresent() ) {
    return;
  }
  //Select one of the cards
  if ( !mfrc522.PICC_ReadCardSerial() ) {
    return;
  }

  String content = "";
  byte letter;
  for ( byte i = 0; i < mfrc522.uid.size; i++ ) {
    content.concat(String(mfrc522.uid.uidByte[i], HEX));
    if ( i < mfrc522.uid.size - 1 ) content += "-";
  }
  content.toUpperCase();
  //Serial.println();
  //Serial.println("UID tag :'" + content + "'");

  if (freeSlot!=0) {

  if( content == "77-39-50-39" ){
    Serial.println("Authorized access");
    myservo.write( 90 );
    
    delay(1000);
    myservo.write( 0 );

  }else{
    Serial.println("Access denied");

  }

  }

  delay(3000);
}

void dataSending_OccupiedSlot(String SensorID, String slotStatus) {

  if (client.connect(server, 1234)) {
    Serial.println("Connection available...");
    Serial.println("Connection available,Sending Data...");
    client.print("GET /Arduinocarpark/data.php?"); // This
    client.print("sensorId=");
    client.print(SensorID);
    client.print("&slotStatus="); // This
    client.print(slotStatus); // And this is what we did in the testing section above. We are making a GET request just like we would from our browser but now with live data from the sensor
    client.println(" HTTP/1.1"); // Part of the GET request
    client.println("Host: 192.168.0.100");
    client.println("Connection: close");
    client.println(); // Empty line
    client.println(); // Empty line
    client.stop();    // Closing connection to server
  }

  else {
    // If Arduino can't connect to the server (your computer or web page)
    Serial.println("--> connection failed\n");
  }

}
