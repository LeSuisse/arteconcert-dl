#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from urllib.request import urlopen
import re
import sys
from xml.dom import minidom


def parse_rtmp_url(url):
    parsed_url = re.search('rtmp://(.*?)/(.*?/.*?)/(.*?)\?', url)
    host = parsed_url.group(1)
    app = parsed_url.group(2)
    playpath = parsed_url.group(3)
    name = re.search('.*?/(\d+.*)', playpath).group(1)
    return 'rtmpdump --host %s --app %s --playpath %s -o %s' % (host, app, playpath, name)


def get_rtmpdump_arteliveweb(url):
    data = urlopen(url).read().decode('utf-8')
    event_id = re.search('eventId=(\d+)', data).group(1)
    urlxml = 'http://download.liveweb.arte.tv/o21/liveweb/events/event-' + event_id + '.xml'
    docxml = minidom.parseString(urlopen(urlxml).read().decode('utf-8'))
    itemlisthd = docxml.getElementsByTagName('urlHd')
    itemlistsd = docxml.getElementsByTagName('urlSd')
    res = {
        'hd': parse_rtmp_url(itemlisthd[0].childNodes[0].nodeValue),
        'sd': parse_rtmp_url(itemlistsd[0].childNodes[0].nodeValue)
    }
    return res

rtmpdumps = get_rtmpdump_arteliveweb(sys.argv[1])
print('SD : ' + rtmpdumps['sd'])
print('HD : ' + rtmpdumps['hd'])