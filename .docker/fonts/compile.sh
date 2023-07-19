#!/bin/bash

set -e

cd /usr/local/src/fonts

for font in /usr/local/src/fonts/*.zip; do
        unzip $font
        rsync -a $(basename $font .zip)/ /usr/local/share/texmf
done

mktexlsr

for map in $(find /usr/local/share/texmf -type f -name '*.map'); do
    cd $(dirname $map)
    updmap-sys --force --enable Map=$(basename $map)
done

mktexlsr

