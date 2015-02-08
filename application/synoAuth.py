#!/usr/bin/env python
#encoding:utf-8

import os
import sys
import cgi,cgitb
import Cookie
import subprocess

class SynoAuth():
	# Constructor
	def __init__(self):
		self.synotoken = ""
		self.username = ""
		self.usergroups = ""
		self.logged = False # By default: not logged
		if "REMOTE_ADDR" in os.environ:
			self.REMOTE_ADDR = os.environ["REMOTE_ADDR"] # Get IP Address
		else:
			self.REMOTE_ADDR = ""
		if 'HTTP_COOKIE' in os.environ: # Get Cookie ID
			cookie_string=os.environ.get('HTTP_COOKIE')
			c=Cookie.SimpleCookie()
			c.load(cookie_string)
			try:
				self.cookie_id=c['id'].value
			except KeyError:
				self.cookie_id=''
		else:
			self.cookie_id=''

	''' Get login information
	If token is provided, use it. If not, use the previously provided one (with setToken method) '''
	def login(self,token=""):
		if token == "":
			token = self.getToken()
		else:
			token = self.setToken(token)

		if token == "":
			return False

		if self._authentificate() == "":
			return False
		self._getGroups()
		return self.getUsername()

	# Call authenticate.cgi with provided synoToken, IP address and Cookie ID in order to get the username
	def _authentificate(self):
		os.environ['QUERY_STRING'] = 'SynoToken=' + self.synotoken.replace("'", "'\\''")
		#os.environ['REMOTE_ADDR'] = self.REMOTE_ADDR,
		os.environ['HTTP_COOKIE'] = 'id=' + self.cookie_id
		cmd = ['/usr/syno/synoman/webman/authenticate.cgi']
		try:
			result = subprocess.check_output(cmd).replace('\n','')
		except:
			result = ""
		if len(result) < 1:
			return False

		self.username = result.replace("\n","")
		return self.getUsername()

	# Get groups
	def _getGroups(self):
		# Only if userdata has already been determined
		if self.username.replace("'", "'\\''") == "":
			return False
		cmd = ['id','-Gn',self.username.replace("'", "\\'")]
		try:
			result = subprocess.check_output(cmd)
		except:
			result = ""
		self.usergroups = result.replace("\n","").split(" ")
		return self.getGroups()

	def getGroups(self):
		return self.usergroups

	def setToken(self,token):
		try:
			self.synotoken = token.replace("'","\\'")
		except:
			print token
			sys.exit()
		return self.getToken()

	def getToken(self):
		return self.synotoken.replace("'","\\'")

	def getUsername(self):
		return self.username.replace("'","\\'")

