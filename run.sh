if ! podman info > /dev/null 2>&1; then
    podman machine start
fi
cd compose || exit
rm -rf ./volumes/log/*
mkdir -p ./volumes/log/mysql
mkdir -p ./volumes/log/apache2
mkdir -p ./volumes/log/php

podman compose --env-file compose.env up --build

