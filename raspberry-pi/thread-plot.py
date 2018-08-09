import os, time, json, requests, sys

# Import SPI library (for hardware SPI) and MCP3008 library.
import Adafruit_GPIO.SPI as SPI
import Adafruit_MCP3008

# Software SPI configuration:
CLK  = 18
MISO = 23
MOSI = 24
CS   = 25
mcp = Adafruit_MCP3008.MCP3008(clk=CLK, cs=CS, miso=MISO, mosi=MOSI)

# define variables
post_key = '' # long random key that matches lamp-server side index.php(front end not insert)
data_file = '/home/pi/Adafruit_Python_MCP3008/examples/turbine-data.txt'
data_sent = '/home/pi/Adafruit_Python_MCP3008/examples/sent.txt'
counter = -1

# write_to
def write_to(file_path, write_data, mode):
    # mode is overwrite w or append a
    f = open(file_path, mode)
    f.write(write_data)
    f.close()
    
def write_failed_upload_log(fail_log):
    f = open('/home/pi/Adafruit_Python_MCP3008/examples/failed-uploads.txt', 'w')
    f.write(str(fail_log) + "\n")
    f.close()

# send data
def send_data():
    global post_key, requests, data_file, data_sent

    t_val_pairs = open(data_file, 'r').read()
    
    if (len(t_val_pairs) > 0):
        turbine_data = {
            'post_key': post_key,
            't_val_pairs': t_val_pairs
        }
        
        try:
            r = requests.post('http://your-domain.com/post', params = turbine_data)
        except requests.exceptions.RequestException as e:
            write_failed_upload_log(e)
    # sys.exit()

    # empty data_file
    write_to(data_file, '', 'w')

    # update data_sent file in case of crash to prevent redundant send or not sending old data
    write_to(data_sent, 'sent', 'w')

# read and write to file
def save_data():
    global mcp, data_file, data_sent, counter

    # deal with counter
    if (counter == -1):
        # was reset or first run
        # check if data_file is not empty
        data_file_val = open(data_file, 'r').read()
        if (data_file_val == ''):
            # check if data_sent says sent
            data_has_sent = open(data_sent, 'r').read()
            if ('sent' not in data_has_sent):
                # must have failed before last batch upload, upload this data
                send_data()
        counter = 0 # set to start value
    elif (counter == 60):
        # send data
        send_data()
        # update data_sent
        write_to(data_sent, '', 'w')
        # reset
        counter = 0
    
    # get data
    a_val = mcp.read_adc(7)
    if (a_val > 0):
        time_stamp = str(time.time()).split('.')[0]
        # check first entry
        if (counter == 0):
            prepend_val = ''
            write_mode = 'w'
        else:
            prepend_val = ';'
            write_mode = 'a'

        cur_data = prepend_val + str(time_stamp) + ',' + str(a_val)

        write_to(data_file, cur_data, write_mode)

while True:
    save_data()
    counter += 1
    time.sleep(1)
