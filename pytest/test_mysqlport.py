import pytest
import socket
import os
from dotenv import load_dotenv
load_dotenv()

base_url = os.environ.get('TEST_HOST')

def test_mysql_port_not_exposed():
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    result = sock.connect_ex((base_url, 3306))
    sock.close()
    assert result != 0, "Port 3306 is exposed!"

def test_mysql_port_not_exposed():
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    result = sock.connect_ex((base_url, 3307))
    sock.close()
    assert result != 0, "Port 3307 is exposed!"

def test_mysql_port_not_exposed():
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    result = sock.connect_ex((base_url, 3360))
    sock.close()
    assert result != 0, "Port 3360 is exposed!"
