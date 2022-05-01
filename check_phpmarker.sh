#!/bin/bash
# このファイルが置いてあるディレクトリで実行
# htmlディレクトリのhtmlファイルに記述されたPHPマーカーを取得する
echo
for f in html/*.html.template; do
  echo "[$f]"
  grep \$\$PHP $f | \
  sed -r 's/[^$]*(\$\$PHP-[^$]*\$\$)[^$]*/\1\n/g' | \
  sed  '/^$/d' | \
  sort | \
  uniq

  echo
done
