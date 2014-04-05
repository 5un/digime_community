var apiData = [
		{
			module:"User",
			description:"User module handles the registration, authentication of users",
			actions:[
				{
					name:"register",
					url:"/auth/register",
					method:"POST",
					desc:"register a user with the desired <code>username</code>, password, email_address and other optional fields",
					params:[
						{name:"username", desc:"Desired username"},
						{name:"password", desc:"Desired password"},
						{name:"email_address", desc:"Desired email address of user"}
					]
				},
				{
					name: "login",
					url:"/auth/login",
					method:"POST",
					desc:"Get access token along with other user informations",
					params:[
						{name:"username", desc:"username of the user"},
						{name:"password", desc:"password of the user"},
						{name:"client_key", desc:"client application key as in oauth_client table"},
						{name:"client_secret", desc:"client application secret as in oauth_client table"}
					]
				},
				{
					name:"getAccessToken",
					url:"/auth/access_token",
					method:"POST",
					desc:"Get access token from a user credential. An access_token is valid for an amount of time.",
					params:[
						{name:"username", desc:"username of the user"},
						{name:"password", desc:"password of the user"},
						{name:"client_key", desc:"client application key as in oauth_client table"},
						{name:"client_secret", desc:"client application secret as in oauth_client table"}
					]
				},
				{
					name:"editProfile",
					url:"/user/editprofile",
					method:"POST",
					desc:"Edit the user profile. The access token of the user must be given along with other fields to update. You can use <code>add more params</code> to add other params",
					params:[
						{name:"access_token", desc:"Access Token of the user"},
						{name:"email_address", desc:"new value of email address to update"}
					]
				},
				{
					name:"editPassword",
					url:"/user/editpassword",
					method:"POST",
					desc:"Change the password of a user with specified access token",
					params:[
						{name:"access_token", desc:"Access Token of the user"},
						{name:"password",desc:"new password to change"}
					]
				}
			]
		},
		{
			module:"News and announcement",
			description:"News module provide user with entries of news, which can also be adapted into blog entries or articles. You can get news by GET to <code>/news</code> and get announcement by <code>/announcement</code>."+
						"<br />announcement can also be sent by XMPP push notification. <a href='' id='btn-notification-panel'>see Notification Panel</a>",
			actions:[
				{
					name:"getNews",
					url:"/news",
					method:"GET",
					desc: "Get an individual news or multiple news item that the property <code>is_published</code> is true."+
							"<p>If an <code>id</code> is given then the call returns a single news that matches the id</p>"+
							"<p>If not then it finds the news that is relevant to <code>query</code>, older than <code> older_than_id</code> with numbers of <code>size</code>",
					params:[
						{name:"id", desc:"The ID of individual news to get"},
						{name:"query",desc:"The query used for searching if <code>id</code> is not present"},
						{name:"older_than_id", desc:"When an <code>id</code> is not given, the result should be older than the news with this id"},
						{name:"size",desc:"Maximum size of the data to return"}
					]
				},
				{
					name:"getAnnouncements",
					url:"/announcement",
					method:"GET",
					desc: "Get the latest announcements. Number of the result is limited by <code>size</code> and be older than <code>older_than_id</code>",
					params:[
						{name:"older_than_id", desc:"If specified, the returned result will be older than this id"},
						{name:"size",desc:"limits the size of the output. If not specified, will be 10"}
					]
				},
				{
					name:"getNotifications",
					url:"/notification",
					method:"GET",
					desc:"Get notifications. ",
					params:[
						{name:"access_token", desc:"The access_token of the user to get the notifications"},
						{name:"older_than_id", desc:"If specified, the result will be older than this id"},
						{name:"size",desc:"limits the size of the output. If not specified of equals 0, a default number of result will be returned"}
					]
				}
			]
		},
		{
			module:"Schedule",
			description:"Schedule module provides happenings in the event. Structure of a schedule item is simply the its <code>title</code>, <code>description</code>, <code>venue_id</code> on which it is held, its preview picture id,<code>tags</code> for searching, <code>start_at</code> and <code>stop_at</code> of the item",
			actions:[
				{
					name:"getSchedules",
					url:"/schedule",
					method:"GET",
					desc:"<p>Get a particular schedule item or multiple items with query. By specifying <code>id</code> the request returns the particular <id>schedule</id> that matches the id</p>"+
						"<p>If <code>id</code> is not specified, schdules that are relevant to <code>query</code> and comes after <code>older_than_id</code> are returned. The <code>size</code> limits the number of results</p>",
					params:[
						{name:"id",desc:"If specified, the api will return the particular <code>schedule</code> that matches"},
						{name:"query",desc:"Query keyword string to search the schedules"},
						{name:"older_than_id",desc:"If specified, the result will be older than the specified id"},
						{name:"size",desc:"limits the number of the result. If not specified will be a default value"}
					]
				},
				{
					name:"getSchedules By date",
					url:"/schedule/day",
					method:"GET",
					desc:"Get schedule items for a day, that is, those schedule items that starts or ends any time in the date of query, or is going on at the start or the end of the day.",
					params:[
						{name:"date",desc:"Date of query. In format 'YYYY-MM-DD'"}
					]
				},
				{
					name:"getUpcomingSchedules",
					url:"/schedule/upcoming",
					method:"GET",
					desc:"Get upcoming schedules, that is schedule items that starts after the queried time ordered by timestamp descended",
					params:[
						{name:"timestamp",desc:"Timestamp of query in the format 'YYYY-MM-DD-HH:mm:ss'"},
						{name:"size", desc:"Limits the number of schedules returned"}
					]
				}
			]
		},
		{
			module:"LiveSession",
			description:"Live Session Module tells the user the available live sessions. User can interact with live seesions by attending or ask questions",
			actions:[
				{
					name:"getLiveSessions",
					url:"/live_session",
					method:"GET",
					params:[
						{name:"id",desc:"The id of live session"},
						{name:"query",desc:"The search keyword"},
						{name:"oldder_than_id",desc:"If specified, the result must be older than the spefified id"},
						{name:"size",desc:"limits the number of rows in resposne"}
					]
				},
				{
					name:"attendLiveSession",
					url:"/live_session/unattend",
					method:"POST",
					params:[
						{name:"access_token", desc:"The access token of user to attend"},
						{name:"live_session_id", desc:""}
					]
				},
				{
					name:"unattendLiveSession",
					url:"/live_session/attend",
					method:"POST",
					params:[
						{name:"access_token",desc:"the sccess token of user to unattend"},
						{name:"live_session_id", desc:""}
					]
				},
				{
					name:"getAttendance",
					url:"/live_session/attendance",
					method:"GET",
					desc:"Check the attendance status of a single user to a live session",
					params:[
						{name:"user_id",desc:"The user_id of the user to check upon"},
						{name:"live_session_id",desc:"The id of the live session to check"}
					]
				},
				{
					name:"getAttendanceList",
					url:"/live_session/attendance_list",
					method:"GET",
					desc:"Get all of the live sessions a user is attending to",
					params:[
						{name:"user_id",desc:"User id to check"}
					]
				}
			]
		},
		{
			module:"LiveSlide",
			description:"LiveSlide modules provides presentation slide show in a live session. The slide could be pushed to device in realtime with XMPP Server. see NotificationPanel",
			actions:[
				{
					name:"getLiveSlide",
					url:"/live_slide",
					method:"GET",
					desc:"Get a particular live slide with id or multiple slide by specifying the <code>live_session_id</code>. If none is specified then request will return with error",
					params:[
						{name:"id",desc:"If specified, result will be the <code>live_slide</code> with matched <code>id</code> if one is found."},
						{name:"live_session_id",desc:"The <code>id</code> of tje <code>live_session</code> to get the slides"},
						{name:"size",desc:"limits the size of the data. If set to 0 or not specified, all slides are returned"},
						{name:"older_than_id",desc:"if specified, the returned slides will be older than this id"}
					]
				},
				{
					name:"bookmarkSlide",
					url:"/live_slide/b",
					method:"POST",
					params:[
						{name:"access_token",desc:"Access Token of the user to bookmark this slide"},
						{name:"live_slide_id",desc:"The slide id to bookmark"}
					]
				},
				{
					name:"unbookmarkSlide",
					url:"/live_slide/ub",
					method:"POST",
					params:[
						{name:"access_token",desc:"Access Token of the user to unbookmark this slide"},
						{name:"live_slide_id",desc:"The slide id to unbookmark"}
					]
				},
				{
					name:"SeeBookmarkedSlides of a user",
					url:"/live_slide/user_bookmarks",
					method:"GET",
					params:[
						{name:"access_token",desc:"The access token of the user to view bookmarks"}
					]
				}
			]
		},
		{
			module:"LiveQA",
			description:"LiveQA module let user ask the question in a live session",
			actions:[
				{
					name:"getQuestionsForLiveSession",
					url:"/live_qa",
					method:"GET",
					desc:"Get questions of the live session specified by <code>live_session_id</code>",
					params:[
						{name:"live_session_id",desc:"The <code>id</code> of the live session to get the answers"},
						{name:"offset",desc:""},
						{name:"page_size",desc:""},
					]
				},
				{
					name:"voteQuestion",
					url:"/live_qa/v",
					method:"POST",
					desc:"Upvote a question",
					params:[
						{name:"access_token", desc:"Access Token of the user to vote"},
						{name:"question_id",desc:"id of the question to upvote"}
					]
				},
				{
					name:"unvoteQuestion",
					url:"/live_qa/uv",
					method:"POST",
					desc:"Unupvote a question",
					params:[
						{name:"access_token", desc:"Access Token of the user to vote"},
						{name:"question_id",desc:"id of the question to unupvote"}
					]
				},
				{
					name:"getUserUpvotes",
					url:"/live_qa/user_upvotes",
					method:"GET",
					desc:"Get all of the upvotes of a user for caching purpose",
					params:[
						{name:"access_token", desc:"Access Token of the user to check"}
					]
				},
				{
					name:"AskNewQuestion",
					url:"/live_qa/aq",
					method:"POST",
					desc:"Post new question by user",
					params:[
						{name:"access_token",desc:"Access Token of the user who posts the question"},
						{name:"live_session_id",desc:"id of the Live Session in which this question posted"},
						{name:"question",desc:"Question string"}
					]
				}
			]
		},
		{
			module:"LivePoll",
			description:"",
			actions:[
				{
					name:"getLivePolls",
					url:"/live_poll",
					method:"GET",
					params:[
						{name:"id",desc:"If specified, The response will be the Live Poll with matched <code>id</code> if found"},
						{name:"older_than_id",desc:"If <code>id</code> is not specified, the result should be older than this id"},
						{name:"size",desc:"limits the number of live polls in response"}
					]
				},
				{
					name:"getLivePollById for user",
					url:"/live_poll/pu",
					method:"GET",
					desc:"Get full livepoll with answer and the vote result of one user",
					params:[
						{name:"access_token",desc:"The access_token of the user to get"},
						{name:"poll_id",desc:"The id of the live poll to get"}
					]
				},
				{
					name:"Refresh live poll",
					url:"/live_poll/pr",
					method:"GET",
					desc:"Refreshes the poll result by just grabbing the minimum poll count. Ideal of realtime result updating",
					params:[
						{name:"access_token",desc:"Access token of the user to vote"},
						{name:"poll_id",desc:"The live poll id to update"}
					]
				},
				{
					name:"Vote on a poll",
					url:"/live_poll/v",
					method:"POST",
					desc:"Vote on an answer. If the poll property <code>is_onetime_mode</code> is 1 and user is voted then this call will fail",
					params:[
						{name:"access_token",desc:"The access token of the user to vote"},
						{name:"poll_id",desc:"The id of the poll to vote"},
						{name:"ans_id",desc:"The answet id to vote"}
					]
				},
				{
					name:"Unvote a poll",
					url:"/live_poll/uv",
					method:"POST",
					desc:"Unvote a poll. This API call simply removes the vote of the user. If the vote property <code>is_onetime_mode</code> then this call will fail",	
					params:[
						{name:"access_token",desc:"The access token of the user to unvote"},
						{name:"poll_id",desc:"The id of the poll to vote"}
					]
				}
			]
		},
		{
			module:"Geolocation",
			description:"Geolocation module provides Point of Interest (POI) data. It can be queried by category, query string or geolocation and radius",
			actions:[
				{
					name:"getPOIs",
					url:"/geolocation",
					method:"GET",
					desc:"",
					params:[
						{name:"id", desc:"If specified, the particular POI with matched id will be returned"},
						{name:"query", desc:"Query string to search data"},
						{name:"size", desc:"The size of the returned query"},
						{name:"lat",desc:"The latitude geolocation in decimal number"},
						{name:"long",desc:"The longituge geolocation in decimal number "},
						{name:"radius",desc:"Radius of search, in metres"}
					]
				}
			]
		},
		{
			module:"Resources",
			description:"All the resources (pictures, files) in digime are referenced as resource id which is exactly the id of the resource in resouce table. To get a resource, send GET request with <code>id</code> to <code>/resource</code>",
			actions:[
				{
					name:"getResource",
					url:"/resource",
					method:"GET",
					desc:"get resource with the resouce's <code>id</code>. If the resouce with the id is non-existent then nothing is returned",
					params:[
						{name:"id", desc:"The id of the resource to get"}
					]
				}
			]
		},
		{
			module:"Photostream",
			description:"Photostream provides the user the photos in the event",
			actions:[
				{
					name:"getPhotos",
					url:"/photostream",
					method:"GET",
					desc:"get the latest photo stream. If <code>older_than_id</code> is specified then the result will be older than the id",
					params:[
						{name:"older_than_id", desc:"If one is specified then the result will be older than the specified id"},
						{name:"size",desc:"limits the size of the result. If not specified then the number of result returned will be a default size"}
					]
				}
			]
		}
	];