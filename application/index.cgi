#!/usr/bin/env python
#encoding:utf-8

'''
 * This is the PHP API which must be called by your application
 * POST or GET Querystrings must be provided:
 * 	synoToken: string of synoToken
 *  action: (optional) string for requested action. 
 * 		"getUserData" by default if not provided
 *
 * Response is JSON style
 * {
 *	rtn: string of return code (200 for success, 401 is no SynoToken has been provided, 402 if SynoToken does not match with existing session
 *  result: {
 *     username: string with username
 *     usergroups: array of string with user's groups
 * }
'''
import os
import sys
import json
import cgi, cgitb 
# Load of SynoAuth class
from synoAuth import *

form = cgi.FieldStorage() 

synoToken = form.getvalue('synoToken')
if synoToken == None:
	synoToken = ""
action = form.getvalue('action')
if action == None:
	action = ""

stream_mode = (action == "streamGetUserData")

# Creation of synoAuth object
synoauth = SynoAuth()

# If no synoToken provided
if synoToken == "" and not stream_mode:
	print "Content-type:text/html\r\n\r\n"
	print(json.dumps({"rtn":'401','error':"No synoToken Provided"}))
	print "\r\n"
	sys.exit()

# Get username corresponding with session
username = synoauth.login(synoToken)

# If no username can be found
if username == False and not stream_mode:
	print "Content-type:text/html\r\n\r\n"
	print(json.dumps({"rtn":'402','error':"Not logged in"}))
	sys.exit()

# Get user's data (username & groups) with stream alive connection
if action == "streamGetUserData":
	print 'Content-Type: text/event-stream\r\n'
	print 'Cache-Control: no-cache\r\n\r\n'
	if username == False:
		print "data:\n\n"
	else:
		print "data:"+username+"\n\n"
	sys.exit()
	
# Get user's data (username & groups)
if action == "getUserData" or True:
	print "Content-type:text/html\r\n\r\n"
	print(json.dumps({"rtn":'200',
						'result':{
							"username": username,
							"usergroups":synoauth.getGroups()
						}
					}))
	sys.exit()

