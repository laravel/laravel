FROM postgres:latest

ONBUILD RUN psql -U project-name -d project-name -c "create extension unaccent;"
