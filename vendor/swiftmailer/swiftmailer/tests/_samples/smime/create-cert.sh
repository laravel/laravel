#!/bin/sh

openssl genrsa -out CA.key 2048
openssl req -x509 -new -nodes -key CA.key -days 1460 -subj '/CN=Swiftmailer CA/O=Swiftmailer/L=Paris/C=FR' -out CA.crt
openssl x509 -in CA.crt -clrtrust -out CA.crt

openssl genrsa -out sign.key 2048
openssl req -new -key sign.key -subj '/CN=Swiftmailer-User/O=Swiftmailer/L=Paris/C=FR' -out sign.csr
openssl x509 -req -in sign.csr -CA CA.crt -CAkey CA.key -out sign.crt -days 1460 -addtrust emailProtection
openssl x509 -in sign.crt -clrtrust -out sign.crt

rm sign.csr

openssl genrsa -out intermediate.key 2048
openssl req -new -key intermediate.key -subj '/CN=Swiftmailer Intermediate/O=Swiftmailer/L=Paris/C=FR' -out intermediate.csr
openssl x509 -req -in intermediate.csr -CA CA.crt -CAkey CA.key -set_serial 01 -out intermediate.crt -days 1460
openssl x509 -in intermediate.crt -clrtrust -out intermediate.crt

rm intermediate.csr

openssl genrsa -out sign2.key 2048
openssl req -new -key sign2.key -subj '/CN=Swiftmailer-User2/O=Swiftmailer/L=Paris/C=FR' -out sign2.csr
openssl x509 -req -in sign2.csr -CA intermediate.crt -CAkey intermediate.key -set_serial 01 -out sign2.crt -days 1460 -addtrust emailProtection
openssl x509 -in sign2.crt -clrtrust -out sign2.crt

rm sign2.csr

openssl genrsa -out encrypt.key 2048
openssl req -new -key encrypt.key -subj '/CN=Swiftmailer-User/O=Swiftmailer/L=Paris/C=FR' -out encrypt.csr
openssl x509 -req -in encrypt.csr -CA CA.crt -CAkey CA.key -CAcreateserial -out encrypt.crt -days 1460 -addtrust emailProtection
openssl x509 -in encrypt.crt -clrtrust -out encrypt.crt

rm encrypt.csr

openssl genrsa -out encrypt2.key 2048
openssl req -new -key encrypt2.key -subj '/CN=Swiftmailer-User2/O=Swiftmailer/L=Paris/C=FR' -out encrypt2.csr
openssl x509 -req -in encrypt2.csr -CA CA.crt -CAkey CA.key -CAcreateserial -out encrypt2.crt -days 1460 -addtrust emailProtection
openssl x509 -in encrypt2.crt -clrtrust -out encrypt2.crt

rm encrypt2.csr
