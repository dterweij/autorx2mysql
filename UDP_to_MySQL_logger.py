#!/usr/bin/env python
#
#   radiosonde_auto_rx - Log to MySQL from UDP data
#
#   Copyright (C) 2019  Danny Terweij <danny@terweij.nl>
#   Used the examples provided from Mark Jessop <vk5qi@rfhead.net> the autor of radiosonde_auto_rx
#   Released under GNU GPL v3 or later
#
#   This code provides an example of how the Horus UDP packets emitted by auto_rx can be received
#   using Python and adding the data into a MySQL database.
#   The data can be used to make a webpage with all kind of statistics.
#
#   Output of Horus UDP packets is enabled using the payload_summary_enabled option in the config file.
#   See here for information: https://github.com/projecthorus/radiosonde_auto_rx/wiki/Configuration-Settings#payload-summary-output
#   By default these messages are emitted on port 55672, but this can be changed.
#
#   You can start the script and watch some output to the screen or start it as script to the background and direct output to dev null
#
#   Version 0.0.1 - june 2019 - Danny Terweij
#

import datetime
import json
import pprint
import socket
import time
import traceback
import MySQLdb as db
import glob
import numpy as np
import sys
import os
from math import radians, degrees, sin, cos, atan2, sqrt, pi
from threading import Thread

#
# Config
#

# Your own location
mylat = 52.789575
mylon = 6.124768
myalt = 9.0

# Database
dbhost = '192.168.0.91'
dbuser = 'autorx'
dbpass = 'f6vrpyqwjxaogW7J'
dbname = 'autorx'

#
# End Config
#

# Some terminal colors as colors are much nicer to use.
CEND      = '\33[0m'
CBOLD     = '\33[1m'
CITALIC   = '\33[3m'
CURL      = '\33[4m'
CBLINK    = '\33[5m'
CBLINK2   = '\33[6m'
CSELECTED = '\33[7m'

CBLACK  = '\33[30m'
CRED    = '\33[31m'
CGREEN  = '\33[32m'
CYELLOW = '\33[33m'
CBLUE   = '\33[34m'
CVIOLET = '\33[35m'
CBEIGE  = '\33[36m'
CWHITE  = '\33[37m'

CBLACKBG  = '\33[40m'
CREDBG    = '\33[41m'
CGREENBG  = '\33[42m'
CYELLOWBG = '\33[43m'
CBLUEBG   = '\33[44m'
CVIOLETBG = '\33[45m'
CBEIGEBG  = '\33[46m'
CWHITEBG  = '\33[47m'

CGREY    = '\33[90m'
CRED2    = '\33[91m'
CGREEN2  = '\33[92m'
CYELLOW2 = '\33[93m'
CBLUE2   = '\33[94m'
CVIOLET2 = '\33[95m'
CBEIGE2  = '\33[96m'
CWHITE2  = '\33[97m'

CGREYBG    = '\33[100m'
CREDBG2    = '\33[101m'
CGREENBG2  = '\33[102m'
CYELLOWBG2 = '\33[103m'
CBLUEBG2   = '\33[104m'
CVIOLETBG2 = '\33[105m'
CBEIGEBG2  = '\33[106m'
CWHITEBG2  = '\33[107m'

def cls():
    # Clear terminal screen
    os.system('cls' if os.name=='nt' else 'clear')

def position_info(listener, balloon):
    radius = 6364963.0

    (lat1, lon1, alt1) = listener
    (lat2, lon2, alt2) = balloon

    lat1 = radians(lat1)
    lat2 = radians(lat2)
    lon1 = radians(lon1)
    lon2 = radians(lon2)

    d_lon = lon2 - lon1
    sa = cos(lat2) * sin(d_lon)
    sb = (cos(lat1) * sin(lat2)) - (sin(lat1) * cos(lat2) * cos(d_lon))
    bearing = atan2(sa, sb)
    aa = sqrt((sa ** 2) + (sb ** 2))
    ab = (sin(lat1) * sin(lat2)) + (cos(lat1) * cos(lat2) * cos(d_lon))
    angle_at_centre = atan2(aa, ab)
    great_circle_distance = angle_at_centre * radius

    ta = radius + alt1
    tb = radius + alt2
    ea = (cos(angle_at_centre) * tb) - ta
    eb = sin(angle_at_centre) * tb
    elevation = atan2(ea, eb)

    distance = sqrt((ta ** 2) + (tb ** 2) - 2 * tb * ta * cos(angle_at_centre))

    if bearing < 0:
        bearing += 2 * pi

    return {
        "listener": listener, "balloon": balloon,
        "listener_radians": (lat1, lon1, alt1),
        "balloon_radians": (lat2, lon2, alt2),
        "angle_at_centre": degrees(angle_at_centre),
        "angle_at_centre_radians": angle_at_centre,
        "bearing": degrees(bearing),
        "bearing_radians": bearing,
        "great_circle_distance": great_circle_distance,
        "straight_distance": distance,
        "elevation": degrees(elevation),
        "elevation_radians": elevation
    }

def degrees_to_cardinal(d):

    dirs = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE",
            "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"]

    ix = int((d + 11.25)/22.5 - 0.02)

    return dirs[ix % 16]

class UDPListener(object):
    ''' UDP Broadcast Packet Listener 
    Listens for Horus UDP broadcast packets, and passes them onto a callback function
    '''

    def __init__(self,
        callback=None,
        summary_callback = None,
        gps_callback = None,
        port=55672):

        self.udp_port = port
        self.callback = callback

        self.listener_thread = None
        self.s = None
        self.udp_listener_running = False


    def handle_udp_packet(self, packet):
        ''' Process a received UDP packet '''
        try:
            # The packet should contain a JSON blob. Attempt to parse it in.
            packet_dict = json.loads(packet)

            # This example only passes on Payload Summary packets, which have the type 'PAYLOAD_SUMMARY'
            # For more information on other packet types that are used, refer to:
            # https://github.com/projecthorus/horus_utils/wiki/5.-UDP-Broadcast-Messages
            if packet_dict['type'] == 'PAYLOAD_SUMMARY':
                if self.callback is not None:
                    self.callback(packet_dict)

        except Exception as e:
            print("Could not parse packet: %s" % str(e))
            traceback.print_exc()


    def udp_rx_thread(self):
        ''' Listen for Broadcast UDP packets '''
        cls()
        print("%s                                         %s") % (CBLUEBG2,CEND)
        print("%s%s  This is Auto_Rx data to MySQL logger.  %s") % (CBLUEBG2,CWHITE,CEND)
        print("%s                                         %s") % (CBLUEBG2,CEND)
        print("")

        self.s = socket.socket(socket.AF_INET,socket.SOCK_DGRAM)
        self.s.settimeout(1)
        self.s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        try:
            self.s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEPORT, 1)
        except:
            pass
        self.s.bind(('',self.udp_port))
        print("%sStarted to listen for UDP packets from Auto_Rx.%s") % (CYELLOW,CEND)
        self.udp_listener_running = True

        # Loop and continue to receive UDP packets.
        while self.udp_listener_running:
            try:
                # Block until a packet is received, or we timeout.
                m = self.s.recvfrom(1024)
            except socket.timeout:
                # Timeout! Continue around the loop...
                m = None
            except:
                # If we don't timeout then something has broken with the socket.
                traceback.print_exc()

            # If we have packet data, handle it.
            if m != None:
                self.handle_udp_packet(m[0])

        print("%sClosing UDP Listener%s") % (CYELLOW,CEND)
        self.s.close()


    def start(self):
        if self.listener_thread is None:
            self.listener_thread = Thread(target=self.udp_rx_thread)
            self.listener_thread.start()


    def close(self):
        self.udp_listener_running = False
        self.listener_thread.join()

def handle_payload_summary(packet):
    ''' Handle a 'Payload Summary' UDP broadcast message, supplied as a dict. '''

    _curtime = datetime.datetime.now().strftime('%d-%m-%Y %H:%M:%S')

    # The station field is provided since auto_rx version 1.1.1
    if 'station' in packet:
        _station = packet['station']
    else:
        _station = "SONDE_AUTO_RX"

    _callsign = packet['callsign']
    _lat = packet['latitude']
    _lon = packet['longitude']
    _alt = packet['altitude']
    _time = packet['time']
    _temp = packet['temp']
    _freq = packet['freq']

    # The frame field isn't provided yet. Waiting for Mark Jessop to add this to the Payload Summary.
    if 'frame' in packet:
        _frame = packet['frame']
    else:
        _frame = "-99"

    # The sats field isn't always provided.
    if 'sats' in packet:
        _sats = packet['sats']
    else:
        _sats = "-99"

    # The batt field is provided since auto_rx version 1.1.2
    if 'batt' in packet:
        _batt = packet['batt']
    else:
        _batt = "-99"

    _hum = packet['humidity']

    # The bt field isn't always provided.
    if 'bt' in packet:
        _bt = packet['bt']
    else:
        _bt = "-99"

    _speed = packet['speed']
    _model = packet['model']

    _last_pos = (_lat,_lon,_alt)
    _stats = position_info((mylat, mylon, myalt), _last_pos)
    _distance = _stats['straight_distance']/1000.0

    # Direction is from your QTH to the Balloon
    _direction = degrees_to_cardinal(_stats['bearing'])

    _evel = _stats['elevation']
    _bear = _stats['bearing']

    # The comment field isn't always provided.
    if 'comment' in packet:
        _comment = packet['comment']
    else:
        _comment = "No Comment Provided"

    #print(_station),
    #print(_callsign),
    #print(_time),
    #print(_frame),
    #print(_alt),
    #print(_temp),
    #print(_freq),
    #print(_distance),
    #print(_speed),
    #print(_batt),
    #print(_sats),
    #print(_model),
    #print(_hum),
    #print(_bt),
    #print(_evel),
    #print(_bear),
    #print(_direction),
    #print(_comment)

    # MySQL processing
    # Needs a error catching
    con = db.connect(host=dbhost, user=dbuser, passwd=dbpass, db=dbname)

    # Check table sondedata to see if we have a new sonde or sonde to be updated.
    query = con.cursor(db.cursors.DictCursor)
    sql = "SELECT * from sondedata WHERE callsign=%s AND station=%s"
    query.execute(sql, (_callsign,_station,))
    record = query.fetchone()

    # I hope this is the way to check if a query result is empty or not ( I dont like this in Pyrhon :-), if you dont check, script errors fly over the screen.. weird stuff) 
    if record == None:
        # New record
        print("%s%s%s %sNew sonde detected %s at station %s%s") % (CRED,_curtime,CEND,CVIOLET,_callsign, _station,CEND)
        
        # Table first_seen
        # This table only hold the very first data seen from a sonde.
        sql = " \
        INSERT INTO first_seen ( station, callsign, time, alt, lat, lon, temp, freq, frame, sats, batt, bt, speed, model, distance, direction, comment, evel, bear, hum ) \
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
        query.execute(sql, ( _station, _callsign, _time, _alt, _lat, _lon, _temp, _freq, _frame, _sats, _batt, _bt, _speed, _model, _distance, _direction, _comment, _evel, _bear, _hum, ))
        con.commit()

        # Table sondedata
        # This table will be updated with latest data from a sonde
        sql = " \
        INSERT INTO sondedata ( station, callsign, time, alt, lat, lon, temp, freq, frame, sats, batt, bt, speed, model, distance, direction, comment, evel, bear, hum ) \
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
        query.execute(sql, ( _station, _callsign, _time, _alt, _lat, _lon, _temp, _freq, _frame, _sats, _batt, _bt, _speed, _model, _distance, _direction, _comment, _evel, _bear, _hum, ))
        con.commit()

    elif ( (record['callsign'] == _callsign and record['station'] == _station) ):
        # Update record
        _id = record['id']
        print("%s%s%s Updating sonde with id %s (%s %s) Freq: %s Distance: %s Km Alt: %s Km Direction: %s") % (CGREEN,_curtime,CEND,_id,_station,_callsign,_freq,round(_distance,1),round(_alt/1000,3),_direction)
 
        # Table sondedata
        # Update the existing data
        sql = "UPDATE sondedata \
        SET time=%s, alt=%s, lat=%s, lon=%s, temp=%s, freq=%s, frame=%s, sats=%s, batt=%s, bt=%s, speed=%s, model=%s, distance=%s, direction=%s, comment=%s, evel=%s, bear=%s, hum=%s \
        WHERE id=%s"
        query.execute(sql, ( _time, _alt, _lat, _lon, _temp, _freq, _frame, _sats, _batt, _bt, _speed, _model, _distance, _direction, _comment, _evel, _bear, _hum, _id, ))
        con.commit()

    con.close()

if __name__ == '__main__':

    # Instantiate the UDP listener.
    udp_rx = UDPListener(
        port=55672,
        callback = handle_payload_summary
        )
    # and start it
    udp_rx.start()

    # From here, everything happens in the callback function above.
    try:
        while True:
            time.sleep(1)
    # Catch CTRL+C nicely.
    except KeyboardInterrupt:
        # Close UDP listener.
        udp_rx.close()
        print("%sUDP listener closed and script exit.%s") % (CGREEN,CEND)
