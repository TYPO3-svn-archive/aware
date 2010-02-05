What works;
- aware creates events every time a record is created or updated.
- aware has a central class, tx_aware, which provides a server side API.
- An extjs client, tx_aware_client, runs in the Typo3 top frame and pulls the server for events IF somebody is interested in them.
- user_notification hooks into alt_doc.php and starts a front-end tx_user_notification_client each time a user edits a record.
- tx_user_notification_client imforms tx_aware_client which events it is interested in; then tx_aware_client polls the server every 5 seconds for these events.
- tx_user_notification_client warns the user when somebody else has saved a new version of the record he is currently working on.

What does not quite work / should be improved:
- A front-end client can not (yet) add events through tx_aware_client.
- tx_aware_client does a request for each channel it is interested in - the requests should be coalesced into a single request.
- tx_aware->getEvents should be extended with a parameter, which states how old events it is interested in. Maybe You would like the same event again, but only if they have not become too old. This could be implemented with channel liftime - but this might not work since the lifetime of a channel might depend on the user.