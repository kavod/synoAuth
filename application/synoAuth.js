<!--

// Class constructor
function SynoAuth()
{
	this.isLogged = false;
	this.error = 0;
	this.synoToken = "";
	this.username = "";
	this.usergroups = new Array();
}

// Get synoToken
SynoAuth.prototype.getSynoToken = function(){
	// Call of login.cgi
	$.ajax({  
		type: "GET", 
		url: "/webman/login.cgi"
	})
	.done(function( data )  
	{
		// Parse login.cgi response
		result = JSON.parse(data);
		// If response matches JSON syntax
		if (typeof result == 'object')
		{
			// If login.cgi indicates active session
			if (result.success)
			{
				// Set isLogged to true
				this.isLogged = true;
				// reset attributes
				this.error = '';
				this.synoToken = result.SynoToken; // save synoToken
				this.username = "";
				this.usergroups = new Array();
				msg = "SynoToken: " + result.result + " - " + this.synoToken;
				console.log(msg);
			} else // If login.cgi indicates no active session
			{
				// Set isLogged to false and save the given reason
				this.isLogged = false;
				this.error = result.reason;
				// reset attributes
				this.synoToken = "";
				this.username = "";
				this.usergroups = new Array();
				msg = "Not logged. Reason: " + result.result + " - " + result.reason;
				console.log(msg);
			}
		} else // if response does not match JSON syntax
		{
			// Set isLogged to false and save the given reason
			this.isLogged = false;
			this.error = "Error during API response";
			// reset attributes			
			this.synoToken = "";
			this.username = "";
			this.usergroups = new Array();
			msg = "Error 400 during synoToken identification";
			console.log(msg);
		}
		
		// Create customEvent "synoToken" in order to notify document about synoToken availability
		var myEvent = new CustomEvent(
			  "synoToken",
			  {
				detail: {
				message: msg,
				time: new Date(),
				synoToken: this.synoToken
				},
			  bubbles: true,
			  cancelable: true
			  }
			);
		// Trigger the event
		$(document).trigger(myEvent);

		// If synoToken is retrieved, get the user's data
		if (this.synoToken!="")
		{
			// Call of PHP API
			$.ajax({  
				type: "GET", 
				url: "/webman/3rdparty/synoAuth/",
				data: {"synoToken": this.synoToken}
			})
			.done(function( data )  
			{
				// If response matches JSON syntax
				result = JSON.parse(data);
				if (typeof result == 'object')
				{
					// If userdata result is OK
					if (result.rtn == "200")
					{
						this.username = result.result.username;
						this.usergroups = result.result.usergroups;
						msg = "Login OK";
					} else // get userdata failed
					{
						this.username = "";
						this.error = result.error;
						this.usergroups = new Array();
						msg = this.error;
					}
				} else // if response does not match JSON syntax
				{
					this.username = "";
					this.error = "Bad server response";
					this.usergroups = new Array();
					msg = this.error;

				}

				// Create customEvent "login" in order to notify document about login data availability
				var myEvent = new CustomEvent(
				  "login",
				  {
					detail: {
					message: msg,
					time: new Date(),
					username: this.username,
					usergroups: this.usergroups
					},
				  bubbles: true,
				  cancelable: true
				  }
				);

				// Trigger this new event
				$(document).trigger(myEvent);
			});
		}
	});
};
//-->
