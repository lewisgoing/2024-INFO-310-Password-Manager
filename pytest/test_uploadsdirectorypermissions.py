import pytest
import requests
import os
from dotenv import load_dotenv
load_dotenv()

base_url = os.environ.get('TEST_HOST')
# Paths to test for potential directory traversal vulnerabilities
# These are common payloads used in directory traversal attacks
# Adjust or extend the list based on your server's structure and known vulnerabilities
TRAVERSAL_PATHS = [
    "/vaults/uploads/pwd_script.php"
]

@pytest.mark.parametrize("path", TRAVERSAL_PATHS)
def test_directory_traversal(path):
    url = "https://" + f"{base_url}{path}"
    response = requests.get(url, verify=False)
    # Ensure the server returns a status code indicating forbidden access or not found
    # Adjust the assert condition based on your server's expected behavior
    assert response.status_code in [403, 404], f"Server side execution {path}"
