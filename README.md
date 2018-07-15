# Turbine Raspi
A little anemometer I made data gathered per second uploaded every minute. [View website](turbineraspi.com)

![v4](https://raw.githubusercontent.com/jdc-cunningham/turbine-raspi/master/v_4_2.jpg)
I'm actually more fond of [v3's](https://raw.githubusercontent.com/jdc-cunningham/turbine-raspi/master/v_3.jpg) design, smaller spoons(cone things).

## Background
This was supposed to be a vertical wind turbine(the helical style) but as of this moment I'm not able to make these work both aerodynamically and structurally(3D printer ideally/probably). I also tried an APC-E model airplane propeller but the placement is not in open wind. Also this is supposedly able to power an ESP8266 Esp-01 but at this time I'm lacking that knowledge regarding level-shifters, storing energy, etc... this motor barely produces anything so I'm going to combine solar and wind and also it would not use the ESP ideally(WiFi) as this would be in the wild at some point eg. using GSM.

## What does this code do?
This code is based on Adafruit's MCP3008(an 8ch ADC) library which my code uses that to grab the analog value(voltage) produced by the anemometer(turbine - dc motor) as it spins. My code is a thread that when ran, every second logs data to a file, then every minute uploads to a remote MySQL database.

# ABSOLUTE PATHS
Ahh feel so stupid, wrote this script ready to go... can't start it... why? ABSOLUTE PATHS... so my systemd/rc.local entries probably worked but were failing because of this. I saw it when I logged the outcome of calling the script by CRON. I'm currently using a `ps axg | grep -c thread-plot` search to see if the process is running so I don't restart it again. <- This is dumb... false readings, always outputs 2 or more... I probably just don't understand it. I used someone's PID method from StackOverflow this seems to be working called by a CRON @reboot entry.

# The anemometer
Yeah it's made from a scavenged DC motor I think from a disc ejector or something, the hub is a foam wheel from an RC plane, some balsa for the "rotors" and then the white cone things at the ends are made from paper plates. I'm worried about when it's super windy, the cones should fold somewhat but most of these anemometers are solid plastic/metal and don't fold... build it better. I'm thinking it will just fail/break/fall when it's that windy though it's generally protected from too much direct wind which defeats its purpose of producing power. Also the power produced is pitiful. We're talking generally 0.01V at best so far though currently the wind speed is like 3mph and again this is in a secluded area that's turbulent.

# The code
The ADC part of this project(convert analog voltage to digital to be sent from python into php -> MySQL database) is based on Adafruit's MCP3008 library(and that ADC) so after I installed that I've just been building stuff on top of it hence the root path.

The included files in this repo:
* Raspberry Pi - home side(where the anemometer is)
  * /home/pi/Adafruit_Python_MCP3008/examples/start-thread-plot.py(this runs at boot by CRON @reboot - runs the thread-plot script as a thread)
    * entry is `@reboot /usr/bin/python /home/pi/Adafruit_Python_MCP3008/examples/start-thread-plot.py > /home/pi/Adafruit_Python_MCP3008/examples/turbine.log 2>&1`
  * /home/pi/Adafruit_Python_MCP3008/examples/thread-plot.py(this logs data every second per minute interval then send data)
  * /home/pi/Adafruit_Python_MCP3008/examples/turbine.log (error logging)
  * /home/pi/Adafruit_Python_MCP3008/examples/turbine-data.txt - stores the every second value(if has analog val for the second)
  * /home/pi/Adafruit_Python_MCP3008/examples/sent.txt - this is in case something goes wrong, pi shuts down/restarts before an upload happens with old data in turbine-data.txt
* LAMP server - remote side which gets and stores the data and presents it, currently mine's just a text dump from the current day
  * php
    * /var/www/html/turbineraspi/index.php
    * /var/www/html/trubineraspi/post - receives GET request from Raspberry Pi with payload in URL parameters
      * index.php
      * db-connect.php
  * mysql
    * database name is turbine_raspi, table name turbine_data
      * turbinde_data.sql
      
## Future
### Charting
So in my other data logging projects for example [Raspi Solar Plotter](raspisolarplotter.com) is using C3.js for charting. I'll do something like that though I have a vision in my mind about using a circular plot style. Not a pie chart but a radius/area based volume visualization I want to try.

Right now it's just a text dump though on the front end.

### Sockets
The other thing I wanted to look into is a web socket where when you view the front end of the site, a web socket connection is initiated and then you can see live data coming from the anemometer "turbine".

### The actual project
The actual project is a self-powered combined solar and wind powered computer with a communication module(GSM most likely). But I have a bit to go with regard to learning how to charge batteries and a dumb situation of where will it go, somewhere safe. I'll probably also have to get my 3D printer running... this will more than likely be several months from now, currently working on trying to get a car haha. Upgrade my peasant wheels(bicycle).
