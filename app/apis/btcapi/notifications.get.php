<?php

	// Prevent exclusive access
	if (!defined("JAWS")) {
		header("HTTP/1.1 401 Unauthorized");
		die();
	}

	// Init Session
	// auth_session_init();

	// // Auth Check - Expecting Session Only !
	// if ((!auth_session_is_logged()) || (!auth_session_is_allowed("batcave"))) {
	// 	header("HTTP/1.1 401 Unauthorized");
	// 	die();
	// }

	// Send Headers
	header('Access-Control-Allow-Credentials: true');
	header("Content-Type: application/json");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-Control: private, must-revalidate, max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");

	/*$notifications = [
		"id" => "root",
		"name" => "Root",
		"sub" => [
			[
				"id" => "categories",
				"name" => "Category",
				"type" => "select",
				"value" => "",
				"options" => [
					[
						"id" => "select",
						"name" => "Select",
						"desc" => "Please select a notification category to see the description.",
					],
					[
						"id" => "courses",
						"name" => "Course",
						"desc" => "Send notification to the students enrolled in a batch of a course.",
						"sub" => [
							[
								"id" => "course",
								"name" => "Sub Category",
								"type" => "select",
								"options" => [
									[
										"id" => "class_reschedule",
										"name" => "Class Reschedule",
										"desc" => "Send notification that a live class has been rescheduled.",
										"sub" => [
											"courses" => [
												"id" => "course",
												"name" => "Course",
												"type" => "select",
												"options" => [],
												"value" => "",
												"inner_sub" => [
													"sections" => [
														"id" => "sections",
														"name" => "Sections",
														"type" => "select",
														"options" => [],
														"value" => "",
														"form" => []
													]
												],
												"source" => [
													"url" => "/notifications/sections",
													"get" => "id"
												]
											]
										],
										"source" => [
											"url" => "/notifications/courses",
											"get" => ""
										]
									],
									[
										"id" => "class_cancel",
										"name" => "Class Cancel",
										"desc" => "Send notification that a live class has been cancelled.",
										"sub" => [
											"courses" => [
												"id" => "courses",
												"name" => "Course",
												"type" => "select",
												"options" => [],
												"value" => "",
												"form" => [
													"sections" => [
														"id" => "sections",
														"name" => "Sections",
														"type" => "select",
														"options" => [],
														"value" => "",
														"form" => []
													]
												],
												"source" => [
													"url" => "/notifications/sections",
													"get" => "id"
												]
											]
										],
										"source" => [
											"url" => "/notifications/courses",
											"get" => ""
										]
									],
									[
										"id" => "contest",
										"name" => "Contest",
										"desc" => "Send notification that a new contest is open.",
										"sub" => [
											"courses" => [
												"id" => "courses",
												"name" => "Course",
												"type" => "select",
												"options" => [],
												"value" => "",
												"form" => [
													"sections" => [
														"id" => "sections",
														"name" => "Sections",
														"type" => "select",
														"options" => [],
														"value" => "",
														"form" => []
													]
												],
												"source" => [
													"url" => "/notifications/sections",
													"get" => "id"
												]
											]
										],
										"source" => [
											"url" => "/notifications/courses",
											"get" => ""
										]
									],
									[
										"id" => "webinar",
										"name" => "Webinar",
										"desc" => "Send notification that a new webinar is live.",
										"sub" => [
											"courses" => [
												"id" => "courses",
												"name" => "Course",
												"type" => "select",
												"options" => [],
												"value" => "",
												"form" => [
													"sections" => [
														"id" => "sections",
														"name" => "Sections",
														"type" => "select",
														"options" => [],
														"value" => "",
														"form" => []
													]
												],
												"source" => [
													"url" => "/notifications/sections",
													"get" => "id"
												]
											]
										],
										"source" => [
											"url" => "/notifications/courses",
											"get" => ""
										]
									]
								]
							]
						]
					],
					[
						"id" => "career",
						"name" => "Careers",
						"desc" => "Send notification regarding a new job opening.",
						"sub" => [],
						"form" => [
							[
								"id" => "title",
								"name" => "Job Title",
								"type" => "text",
								"value" => ""
							],
							[
								"id" => "company",
								"name" => "Company Name",
								"type" => "text",
								"value" => ""
							],
							[
								"id" => "logo",
								"name" => "Company Logo",
								"type" => "file",
								"value" => ""
							],
							[
								"id" => "code",
								"name" => "Jod Code",
								"type" => "text",
								"value" => ""
							],
							[
								"id" => "location",
								"name" => "Location",
								"type" => "text",
								"value" => ""
							],
							[
								"id" => "designation",
								"name" => "Designation",
								"type" => "text",
								"value" => ""
							],
							[
								"id" => "experience",
								"name" => "Experience",
								"type" => "number",
								"value" => ""
							],
							[
								"id" => "tools",
								"name" => "Tools",
								"type" => "text",
								"value" => ""
							],
							[
								"id" => "domain",
								"name" => "Domain",
								"type" => "text",
								"value" => ""
							],
							[
								"id" => "description",
								"name" => "Description",
								"type" => "text",
								"value" => ""
							],
							[
								"id" => "on",
								"name" => "Interview Date",
								"type" => "date",
								"value" => ""
							],
							[
								"id" => "created_at",
								"name" => "Posted on",
								"desc" => "Pick the date this notification is posted",
								"type" => "date",
								"value" => ""
							],
							[
								"id" => "deadline",
								"name" => "Submission deadline",
								"type" => "date",
								"value" => ""
							]
						]
					],
					[
						"id" => "webinar",
						"name" => "Webinars",
						"desc" => "Send notification of a new webinar or rescheduling of a current webinar.",
						"sub" => [],
						"form" => []
					],
					[
						"id" => "global",
						"name" => "Global",
						"desc" => "Send notification to all the registered mobile application users.",
						"sub" => [
							[
								"id" => "announcements",
								"name" => "Announcements",
								"desc" => "Send general notifications like holiday / new course launch / new features added to mobile app or JLC."
							]
						],
						"form" => []
					]
				]
			]
		]
	];*/

	$notifications = [
		"id" => "category",
		"name" => "Category",
		"type" => "select",
		"options" => [
			[
				"id" => "select",
				"name" => "Select",
				"type" => "placeholder"
			],
			[
				"id" => "course",
				"name" => "Course",
				"type" => "select",
				"options" => [],
				"source" => [
					"url" => "/notifications/courses",
				],
				"sub" => [
					"id" => "section",
					"name" => "Section",
					"type" => "select",
					"options" => [],
					"source" => [
						"url" => "/notifications/sections",
						"get" => "id"
					],
					"sub" => [
						"id" => "sub_category",
						"name" => "Sub Category",
						"type" => "select",
						"options" => [
							[
								"id" => "class_reschedule",
								"name" => "Class Reschedule",
								"type" => "form",
								"title" => "Class has been rescheduled"
							],
							[
								"id" => "class_cancel",
								"name" => "Class Cancel",
								"type" => "form",
								"title" => "Class has been cancelled"
							],
							[
								"id" => "contest",
								"name" => "Contest",
								"type" => "form",
								"title" => "A new contest is live"
							],
							[
								"id" => "webinar",
								"name" => "Webinar",
								"type" => "form",
								"title" => "A new webinar is live!",
								"nr" => [
									"presenter"
								]
							]
						]
					]
				]
			],
			[
				"id" => "career",
				"name" => "Careers",
				"type" => "form",
				"title" => "New job opening is available"
			],
			[
				"id" => "webinar",
				"name" => "Webinar",
				"type" => "form",
				"title" => "A new webinar is live!",
			],
			[
				"id" => "global",
				"name" => "Global"
			]
		]
	];

	$forms = [
		"class_reschedule" => [
			[
				"id" => "name",
				"name" => "Class Name",
				"type" => "text",
			],
			[
				"id" => "from",
				"name" => "From",
				"type" => "datetime-local"
			],
			[
				"id" => "to",
				"name" => "To",
				"type" => "datetime-local"
			]
		],
		"class_cancel" => [
			[
				"id" => "name",
				"name" => "Class Name",
				"type" => "text",
			],
			[
				"id" => "at",
				"name" => "At",
				"type" => "datetime-local"
			]
		],
		"content" => [
			[
				"id" => "title",
				"name" => "Title",
				"type" => "text"
			],
			[
				"id" => "submit_at",
				"name" => "Submission By",
				"type" => "datetime-local"
			],
			[
				"id" => "Tools",
				"name" => "Tools",
				"type" => "text"
			],
			[
				"id" => "desc",
				"name" => "Description",
				"type" => "text"
			],
			[
				"id" => "image",
				"name" => "Images",
				"type" => "file"
			]
		],
		"webinar" => [
			[
				"id" => "title",
				"name" => "Title",
				"type" => "text"
			],
			[
				"id" => "presenter",
				"name" => "Presenter",
				"type" => "text"
			],
			[
				"id" => "image",
				"name" => "Image",
				"type" => "file"
			],
			[
				"id" => "at",
				"name" => "When",
				"type" => "datetime-local"
			],
			[
				"id" => "desc",
				"name" => "Description",
				"type" => "text"
			],
			[
				"id" => "link",
				"name" => "Joining Link",
				"type" => "url"
			]
		],
		"career" => [
			[
				"id" => "title",
				"name" => "Job Title",
				"type" => "text"
			],
			[
				"id" => "code",
				"name" => "Job Code",
				"type" => "text"
			],
			[
				"id" => "company",
				"name" => "Company Name",
				"type" => "text",
				"value" => ""
			],
			[
				"id" => "logo",
				"name" => "Company Logo",
				"type" => "file",
				"value" => ""
			],
			[
				"id" => "location",
				"name" => "Location",
				"type" => "text",
				"value" => ""
			],
			[
				"id" => "designation",
				"name" => "Designation",
				"type" => "text",
				"value" => ""
			],
			[
				"id" => "experience",
				"name" => "Experience",
				"type" => "number",
				"value" => ""
			],
			[
				"id" => "tools",
				"name" => "Tools",
				"type" => "text",
				"value" => ""
			],
			[
				"id" => "domain",
				"name" => "Domain",
				"type" => "text",
				"value" => ""
			],
			[
				"id" => "description",
				"name" => "Description",
				"type" => "text",
				"value" => ""
			],
			[
				"id" => "on",
				"name" => "Interview Date",
				"type" => "date",
				"value" => ""
			],
			[
				"id" => "posted_on",
				"name" => "Posted on",
				"desc" => "Pick the date this notification is posted",
				"type" => "date",
				"value" => ""
			],
			[
				"id" => "reply_by",
				"name" => "Submission deadline",
				"type" => "date",
				"value" => ""
			]
		]
	];

	die(json_encode(["notifications" => $notifications, "forms" => $forms]));

?>