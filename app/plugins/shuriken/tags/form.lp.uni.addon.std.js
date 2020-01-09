_vf.push({	
	"rv":'\\b(name)|(first name)',
	"fr": [ 
		{
			"rule" : "req",
			"message" : "Please provide input."
		},
		{
			"rule" : "nospec",
			"message" : "Special Characters not permitted."
		},
		{
			"rule" : "nonum",
			"message" : "Numbers not allowed."
		}
	] 
});
_vf.push({
	"rv":"email|email address", 
	"fr": [ 
		{
			"rule" : "req",
			"message" : "Please provide input."
		},
		{
			"rule" : "email",
			"message" : "Please provide email address."
		}
	] 
});
_vf.push({
	"rv":"phone|phone number", 
	"fr": [ 
		{
			"rule" : "req",
			"message" : "Please provide input."
		},
		{
			"rule" : "nospec",
			"message" : "Special Characters not permitted."
		},
		{
			"rule" : "phone",
			"message" : "Provide valid phone number."
		}
	] 
});