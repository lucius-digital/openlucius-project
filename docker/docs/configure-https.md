# Configure HTTPS

## Generate an SSL certificate

Generate self-signed certificates for local development on
**https://openlucius.localhost**. Since chrome 58, certificates without that don't have a
proper **subject alternative name** are rejected by google and mozilla.

Make sure you have `openssl` installed. If not: `brew install openssl`.
Then, execute the following command in the project root.

### Create certificate and key

```sh
openssl genrsa 4096 \
| tee ./docker/proxy/cert.key \
| openssl req -new -subj "/CN=openlucius.localhost" -key /dev/stdin -config ./docker/conf/openlucius.conf \
| openssl x509 -req -days 3650 -in /dev/stdin -signkey ./docker/proxy/cert.key -extensions v3_req -extfile ./docker/conf/openlucius.conf \
>./docker/proxy/cert.crt

# For debugging purposes: check certificate
openssl x509 -in ./docker/proxy/cert.crt -noout -text
```

`cert.key` and `cert.crt` will be created in `docker/proxy/`.

You should add `cert.crt` to **keychain access** and mark it as explicitly
trusted to handle SSL traffic.

### Add to keychain
Import `cert.crt` into keychain with **File > Import Items** and set the
certificate as trusted for SSL traffic as explained in step 2 of this tutorial:
[generate SSL certificate tutorial](https://certsimple.com/blog/localhost-ssl-fix)

Note: certificate files are added to `.gitignore` so your certificate will not
be under revision control.
