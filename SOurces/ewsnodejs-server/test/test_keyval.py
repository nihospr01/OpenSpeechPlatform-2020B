import requests
import base64
import json
import pytest

def setup_module():
    """ setup any state specific to the execution of the given module."""
    requests.post("http://localhost:5000/api/db/table/create/test_table")

def teardown_module():
    """ teardown any state that was previously setup with a setup_module
    method.
    """
    requests.delete("http://localhost:5000/api/db/table/delete/test_table")

def test_create_table():
    URL = "http://localhost:5000/api/db/table/create/table1"
    res = requests.post(URL)
    # print(res)
    # print(res.text)

    assert res.status_code == 200
    assert res.text == ""
    # should fail if trying to create existing table
    res = requests.post(URL)
    assert res.status_code == 404
    assert res.text == "table with that name already exists"

def test_delete_table():
    URL = "http://localhost:5000/api/db/table/delete/table1"
    res = requests.delete(URL)
    # print(res)
    # print(res.text)

    assert res.status_code == 200
    assert res.text == ""

    # will fail to delete non-existant
    res = requests.delete(URL)
    assert res.status_code == 500
    assert res.text == "table with that name does not exist"


def test_key():
    # Add key:key_test value:value_test to db
    URL = "http://localhost:5000/api/db/test_table"

    headers = {"Content-Type": "application/json"}
    body = json.dumps({"key": "key_test2", "value": "value1"})
    res = requests.post(URL, headers=headers, data=body)
    assert res.status_code == 200
    assert res.text == ""

    # get it
    body = json.dumps({"key": "key_test2"})
    res = requests.get(URL, headers=headers, data=body)
    assert res.text == "value1"

    # set it to new value
    body = json.dumps({"key": "key_test2", "value": "value2"})
    res = requests.post(URL, headers=headers, data=body)
    assert res.status_code == 200
    assert res.text == ""

    # get the new value
    body = json.dumps({"key": "key_test2"})
    res = requests.get(URL, headers=headers, data=body)
    assert res.text == "value2"

    # delete key
    res = requests.delete(URL, headers=headers, data=body)
    assert res.status_code == 200
    assert res.text == ""

    # attempt to read key returns empty string and 500
    res = requests.get(URL, headers=headers, data=body)
    assert res.status_code == 500
    assert res.text == "key not found"

def test_get_all():

    # set some values
    URL = "http://localhost:5000/api/db/test_table"
    headers = {"Content-Type": "application/json"}
    keys = ['ant', 'bear', 'cat', 'dog', 'eagle']
    for i, k in enumerate(keys):
        body = json.dumps({"key": k, "value": i})
        res = requests.post(URL, headers=headers, data=body)
        assert res.status_code == 200

    # get the values.  don't set a key in the body
    res = requests.get(URL)

    # got the right number?
    assert len(res.json()) == len(keys)

    # verify the values of the keys
    for k in res.json():
        body = json.dumps({"key": k})
        res = requests.get(URL, headers=headers, data=body)
        assert res.status_code == 200
        # print(k, res.text, keys.index(k))
        assert int(res.text) == keys.index(k)

# setup_module()
# test_create_table()
# test_delete_table()
# test_key()
# test_get_all()
# teardown_module()
