cd /tmp
git clone https://github.com/rsky/php-mecab.git
cd php-mecab/mecab
/app/php/bin/phpize
./configure --with-mecab=/usr/local/src/mecab-0.996/mecab-config
make
make install
cd ../../