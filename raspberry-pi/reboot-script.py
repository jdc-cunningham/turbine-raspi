import os

# CRON will call this script every 5 minutes to ping a remote ip
# restart if it fails
# the thread plot starts on reboot with a PID to prevent redundant calls

# reconnect to WiFi by restarting
hostname = 'www.example.com' # or other domain
response = os.system('ping -c 1 ' + hostname)
if response != 0:
    from subprocess import call
    call("sudo shutdown -r now", shell=True)
    exit()
