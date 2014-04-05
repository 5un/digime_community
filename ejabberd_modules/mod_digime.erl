%%%
%%% mod_digime.erl
%%% sun@digimagic.com.sg
%%%

-module(mod_digime).
-author('sun@digimagic.com.sg').

-behavior(gen_mod).

-export([start/2, stop/1,
	send_message_announcement/5,
	send_message_notification/3,
	send_message_liveslide/3,
	send_message_livepoll/3
]).

-include("ejabberd.hrl").
-include("jlib.hrl").

%%%
%%% gen_mod
%%%

start(_Host,_Opts)->
	ok.
	
stop(_Host)->
	ok.
	
%%%
%%% notification functions
%%%

send_message_announcement(From, To, Id, Title, Body) ->
	Packet = build_packet(message_announcement, [Id, Title, Body]),
	send_packet_all_resources(From, To, Packet).

send_message_notification(From, To, NotificationPayload) ->
	Packet = build_packet(message_notification, NotificationPayload),
	send_packet_all_resources(From, To, Packet).
	
send_message_liveslide(From, To, SlidePayload) ->
	Packet = build_packet(message_liveslide, SlidePayload),
	send_packet_all_resources(From, To, Packet).
	
send_message_livepoll(From, To, PollPayload) ->
	Packet = build_packet(message_livepoll, PollPayload),
	send_packet_all_resources(From, To, Packet).
	
%% From mod_admin_extra
%%
%% @doc Send a packet to a Jabber account.
%% If a resource was specified in the JID,
%% the packet is sent only to that specific resource.
%% If no resource was specified in the JID,
%% and the user is remote or local but offline,
%% the packet is sent to the bare JID.
%% If the user is local and is online in several resources,
%% the packet is sent to all its resources.

send_packet_all_resources(FromJIDString, ToJIDString, Packet) ->
    FromJID = jlib:string_to_jid(FromJIDString),
    ToJID = jlib:string_to_jid(ToJIDString),
    ToUser = ToJID#jid.user,
    ToServer = ToJID#jid.server,
    case ToJID#jid.resource of
	"" ->
	    send_packet_all_resources(FromJID, ToUser, ToServer, Packet);
	Res ->
	    send_packet_all_resources(FromJID, ToUser, ToServer, Res, Packet)
    end.

send_packet_all_resources(FromJID, ToUser, ToServer, Packet) ->
    case ejabberd_sm:get_user_resources(ToUser, ToServer) of
	[] ->
	    send_packet_all_resources(FromJID, ToUser, ToServer, "", Packet);
	ToResources ->
	    lists:foreach(
	      fun(ToResource) ->
		      send_packet_all_resources(FromJID, ToUser, ToServer,
						ToResource, Packet)
	      end,
	      ToResources)
    end.

send_packet_all_resources(FromJID, ToU, ToS, ToR, Packet) ->
    ToJID = jlib:make_jid(ToU, ToS, ToR),
    ejabberd_router:route(FromJID, ToJID, Packet).
	
%%%
%%% build_packet
%%%
	
build_packet(message_announcement, [Id, Title, Body]) ->
	{xmlelement, "message",
	 [{"type", "chat"}, {"id", randoms:get_string()}],
		[{xmlelement, "announcement",[{"xmlns", "http://www.digimagic.com.sg/digime/xmppns"}], [
				{xmlelement, "id", [], [{xmlcdata, Id}]},
				{xmlelement, "title", [], [{xmlcdata, Title}]},
				{xmlelement, "body", [], [{xmlcdata, Body}]}
			]}
		]
	};
build_packet(message_notification, NotificationPayload)->
	{xmlelement, "message",[{"type","chat"}, {"id",randoms:get_string()}],
		[{xmlelement, "notification", [{"xmlns", "http://www.digimagic.com.sg/digime/xmppns"}], [
			{xmlcdata, NotificationPayload}
		]}]
	};
build_packet(message_liveslide, SlidePayload)->
	{xmlelement, "message",[{"type","chat"},{"id",randoms:get_string()}],
		[{xmlelement, "liveslide", [{"xmlns", "http://www.digimagic.com.sg/digime/xmppns"}], [
			{xmlcdata, SlidePayload}
		]}]
	};
build_packet(message_livepoll, PollPayload)->
	{xmlelement, "message", [{"type","chat"}, {"id", randoms:get_string()}],
		[{xmlelement, "livepoll", [{"xmlns", "http://www.digimagic.com.sg/digime/xmppns"}], [
			{xmlcdata, PollPayload}
		]}]
	}.
	