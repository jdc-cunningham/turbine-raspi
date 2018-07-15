import os, sys, threading
from threading import Thread

# start while true every second script
def data_upload_start():
    os.system('/usr/bin/python /home/pi/Adafruit_Python_MCP3008/examples/thread-plot.py')

# prevent running multiple instances
pid = str(os.getpid())
pidfile = "/tmp/turbineraspi.pid"

if os.path.isfile(pidfile):
    sys.exit()
file(pidfile, 'w').write(pid)
try:
    Thread(target=data_upload_start).start()
finally:
    os.unlink(pidfile)