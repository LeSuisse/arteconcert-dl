#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from urllib.request import urlopen
import sys
import json
from lxml import html


def get_video_page(url):
    page = html.parse(url)
    urlres = page.xpath('//@arte_vp_url')
    if len(urlres) == 0:
        raise Exception("La page contenant la vidéo n'a pas pu être traitée")
    return urlres[0]


def get_url_arteconcert(url):
    data = urlopen(get_video_page(url)).read().decode('utf-8')
    video_urls = json.loads(data)
    res = {}
    try:
        for type, value in video_urls['videoJsonPlayer']['VSR'].items():
            if value['quality'] != "":
                res[value['quality']] = value['url']
    except KeyError:
        raise Exception("L'URL de la vidéo n'a pu être trouvée")
    return res


if __name__ == '__main__':
    try:
        urls = get_url_arteconcert(sys.argv[1])
        for quality, url in urls.items():
            print(quality + ' : ' + url)
    except Exception as err:
        sys.stderr.write(str(err))
