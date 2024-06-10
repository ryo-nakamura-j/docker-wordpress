## Add local certificates here to enable https

If you have to create a certification by your own you can simply navigate to this folder and run the following command:

where [DOMAIN] is your domain name e.g. website.local
`mkcert -ecdsa -cert-file ./[DOMAIN].pem -key-file ./[DOMAIN]-key.pem [DOMAIN] 127.0.0.1 ::1`
