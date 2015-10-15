#!/usr/bin/python

"""
Check If Command To Execute
"""

import sys
import os

if os.path.isfile("/etc/domokine/command"):
	file = open('/etc/domokine/command', 'r')
	command=file.read()
	os.system(command)
	os.remove('/etc/domokine/command'