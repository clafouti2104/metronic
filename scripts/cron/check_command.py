#!/usr/bin/python

"""
Check If Command To Execute
"""

import sys
import file
import os.path

if os.path.isfile("/etc/domokine/command"):
	file = open('/etc/domokine/command', 'r')
	print file.read()
