#include <TinyGPS++.h>
#include <SoftwareSerial.h>


TinyGPSPlus gps; 
SoftwareSerial SoftSerial(2, 3);

int onModulePin=4;

int8_t answer; 
char aux_string[30];

//
char phone_number[]="+221773921334"; // le numero vers lequel on envoie les coordonnees GPS *****
unsigned short timer=millis();
unsigned short ms = 60000; //période d'envoie des coordonnnées au serveur *****
unsigned short dt = 500; // = 0.5s: periode recuperation des coordonnées GPS

byte gps_state=0;

//derniere position
double last_lat=0;
double last_lng=0;
double last_alt=0;

//position courante
double cur_lat=0;
double cur_lng=0;
double cur_alt=0;

double  distance=0;
double distance_seuille = 30; //distance minimale entre deux 2 arrets ***
char matricule_bus [] = "10111dk"; //***
char ligne [] = "ligne 10"; //***



void setup() 
{ 
  //---------------------------------------------------------------------------INITIALISATION DU MODULE GPS
  SoftSerial.begin(9600);  // the SoftSerial baud rate  
  //---------------------------------------------------------------------------FIN INITIALISATION DU MODULE GPS
  
  //---------------------------------------------------------------------------INITIALISATION DU MODULE GSM
    Serial.begin(9600);
    pinMode(onModulePin, OUTPUT);
    //wait(3000);
    initGSM();
  //---------------------------------------------------------------------------FIN INITIALISATION DU MODULE GSM
} 

//-----------------------------------
void loop() 
{ 
  if(SoftSerial.available() > 0)
   if(gps.encode(SoftSerial.read())){
      //  distance=TinyGPSPlus::distanceBetween(last_lat,last_lng,gps.location.lat(),ps.location.lng());
        gps_state=1; 
   }
   
      
// if((unsigned long)(millis() - timer > ms)&&(gps_state==1)){
 if((unsigned long)(millis() - timer > dt)&&(gps_state==1)){
      tracking();
      timer = millis();
    }
}

void getGPsData(){};

//------------------------------------
void initGSM(){
    if (sendATcommand("AT", "OK", 100) == 0)
    {

        digitalWrite(onModulePin,HIGH); 
        wait(100);
        digitalWrite(onModulePin,LOW);
    
        while(sendATcommand("AT", "OK", 100) == 0);
    }
    
  while(sendATcommand("AT+CREG?", "+CREG: 0,1", 2000) == 0 );
    
  sprintf(aux_string,"AT+CMGS=\"%s\"", phone_number);
  
  if(sendATcommand("AT+CMGF=1", "OK", 1000)){
      wait(50);
      sendATcommand(aux_string, ">", 2000);
      wait(50);
      Serial.print("Synchronisation du bus");
      Serial.write(0x1A);
      sendATcommand("AT", "OK", 100);
   }
}


void sendGpsData(){
  
  if(gps.location.isUpdated()){
      cur_lat = gps.location.lat();
      cur_lng = gps.location.lng();
      cur_alt = gps.altitude.meters();
      
      distance = TinyGPSPlus::distanceBetween(last_lat, last_lng, cur_lat, cur_lng);
      
      if(distance >= distance_seuille){

          last_lat = cur_lat;
          last_lng = cur_lng;
          last_alt = cur_alt;
          
           if(sendATcommand("AT+CMGF=1", "OK", 1000)==1){
              wait(50);
              sendATcommand(aux_string, ">", 2000);
              wait(50);
              Serial.print(matricule_bus); //matricule
              Serial.print(" ");
              Serial.print(ligne); //ligne
              Serial.print(" ");
              Serial.print(last_lat, 6); //Recuperation et ecriture de la latitude actuelle sur le SMS
              Serial.print(" ");
              Serial.print(last_lng, 6); //Recuperation et ecriture de la longitude actuelle sur le SMS
              Serial.print(" ");
              Serial.print(last_alt, 4); 
              Serial.print(" ");
              Serial.print(gps.speed.kmph(), 6); //Recuperation et ecriture de la vitesse actuelle sur le SMS
              Serial.print(" ");
              Serial.print("2");
              Serial.print(" ");
              Serial.print(gps.time.value()); //Recuperation et ecriture de l'heure actuelle sur le SMS
              Serial.print(" ");
              Serial.print(gps.date.value()); //Recuperation et ecriture de la date actuelle sur le SMS
              Serial.print(" ");
              Serial.write(0x1A); //la fin
          }
      }
    
   }
}

   
int8_t sendATcommand(char* ATcommand, char* expected_answer, unsigned int timeout){
    uint8_t x=0,  answer=0;
    char response[100];
    unsigned long previous;
    
    // Initialisation de la chaine de caractère (string).
    memset(response, '\0', 100);
    
    wait(100);
    
    while( Serial.available() > 0)
      Serial.read();

    Serial.println(ATcommand);
    previous = millis();
    
    // Cette boucle attend la réponse du module GSM.
    do{  
          if(Serial.available() != 0){  
    
            response[x] = Serial.read();
            x++;
    
            // Comparaison des données
            if (strstr(response, expected_answer) != NULL)    
            {
                answer = 1;
            }
        }else{
          
          }
    
    // Attente d'une réponse.
    }while((answer == 0) && ((millis() - previous) < timeout)); 
       
    return answer;
}

byte isStopped(){
  
}

static void wait(unsigned long ms)
{
  unsigned long start = millis();
  while (millis() - start < ms);
}

void tracking(){
  
  if(gps.location.isUpdated()){
      cur_lat = gps.location.lat();
      cur_lng = gps.location.lng();
      cur_alt = gps.altitude.meters();
      
      distance += TinyGPSPlus::distanceBetween(last_lat, last_lng, cur_lat, cur_lng);

      last_lat = cur_lat;
      last_lng = cur_lng;
      last_alt = cur_alt;
      
      if(distance >= distance_seuille){
        sendGpsData();
        distance = 0;
      }

      
    
   }
}

