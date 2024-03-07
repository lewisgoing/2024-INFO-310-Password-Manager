from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
import os
from dotenv import load_dotenv

load_dotenv()  # This loads the environment variables from a .env file

class TestLoginAndCookieFlags:
    def setup_method(self):
        self.driver = webdriver.Chrome()
        self.vars = {}

    def teardown_method(self):
        self.driver.quit()

    def test_login_and_phpsessid_cookie_flags(self):
        # Use os.environ.get to get the environment variable
        base_url = os.environ.get('TEST_HOST', 'default_url_if_not_set')
        self.driver.get(f"https://{base_url}/login.php")
        self.driver.set_window_size(2560, 1080)

        # Assuming 'username' and 'password' are the IDs for the login inputs
        # We should probably use env variables for the login creds :/
        self.driver.find_element(By.ID, "username").send_keys("username")
        self.driver.find_element(By.ID, "password").send_keys("password!")
        self.driver.find_element(By.ID, "password").send_keys(Keys.ENTER)

        # After login, check the PHPSESSID cookie
        cookies = self.driver.get_cookies()
        phpsessid_cookie = next((cookie for cookie in cookies if cookie['name'] == 'PHPSESSID'), None)

        # Verify the PHPSESSID cookie exists
        assert phpsessid_cookie is not None, "PHPSESSID cookie not found."

        # Ensure the HttpOnly flag is set
        assert phpsessid_cookie.get('httpOnly'), "PHPSESSID cookie lacks the 'HttpOnly' flag."

        # Ensure the Secure flag is set
        assert phpsessid_cookie.get('secure'), "PHPSESSID cookie lacks the 'Secure' flag."
