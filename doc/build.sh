docker run -p 35729:35729 -p 4000:4000 --rm -v "$PWD:/srv/jekyll" jekyll/minimal:latest sh -c 'chown -R jekyll /usr/gem && jekyll serve --incremental'
