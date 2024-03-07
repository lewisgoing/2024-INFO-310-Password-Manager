import pytest
import time
import json
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities
from dotenv import load_dotenv
load_dotenv()  # This loads the environment variables from a .env file

class TestBadLoginCreds():
    def setup_method(self, method):
        self.driver = webdriver.Chrome()
        self.vars = {}
            
    def teardown_method(self, method):
        self.driver.quit()
    
    def test_badLoginCreds(self):
        # Use os.environ.get to get the environment variable
        base_url = os.environ.get('TEST_HOST', 'default_url_if_not_set')
        self.driver.get("https://" + base_url + "/login.php")
        self.driver.set_window_size(2560, 1080)
        self.driver.find_element(By.ID, "password").send_keys("badpass")
        self.driver.find_element(By.ID, "username").send_keys("badguy")
        self.driver.find_element(By.CSS_SELECTOR, ".btn-primary").click()
        assert self.driver.find_element(By.CSS_SELECTOR, ".alert").text == "Invalid username or password."
