import os
from dotenv import dotenv_values

config = dotenv_values(".env")  # take environment variables from .env.

def test_test_host_present():
    test_host = os.getenv("TEST_HOST")
    print(config)
    # get the current working directory
    current_working_directory = os.getcwd()
    # print output to the console
    print(current_working_directory)
    assert test_host is not None, "TEST_HOST environment variable is not set"
