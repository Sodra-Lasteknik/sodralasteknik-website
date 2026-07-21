#!/usr/bin/env python3
"""Enkel lokal utvecklingsserver för Nordö-sajten.

Kör:  python serve.py
Öppna sedan http://localhost:8000

Obs: PHP-formuläret (contact/kontakt.php) körs inte av den här servern.
För att testa formuläret lokalt, använd i stället PHP:s inbyggda server:
    php -S localhost:8000
"""
import http.server
import socketserver

PORT = 8000


class Handler(http.server.SimpleHTTPRequestHandler):
    def end_headers(self):
        self.send_header("Cache-Control", "no-store")
        super().end_headers()


if __name__ == "__main__":
    with socketserver.TCPServer(("", PORT), Handler) as httpd:
        print(f"Nordö-sajten körs på http://localhost:{PORT}  (Ctrl+C för att avsluta)")
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            print("\nServern avslutad.")
