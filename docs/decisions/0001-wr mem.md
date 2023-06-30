# "wr mem" Command
Architecture Decision Record  
Lang: en  
Encoding: utf-8  
Date: 2023-06-29  
Author: Georg Sieber

## Decision
Do not recommend to execute `wr mem` after each save procedure anymore (`const DO_WR_MEM` configured in config.php) as it can lead to infinite page loading times.

## Status
Accepted

## Context
In some cases, the `exit` command, which ends the SSH session, is executed before the Cisco IOS shell returns to the prompt after executing `wr mem`. As a result, the SSH session is not terminated and the web page loads infinitely.
```
switch1#wr mem
Building configuration...
exit
[OK]!

... SSH session remaining active ...
```

It's not entirely clear why this happens sometimes. On other switches, it works perfectly with output like this:
```
switch2#wr mem
Building configuration...
[OK]!
switch2#exit

... SSH session exited ...
```

## Consequences
The switch configuration must be persisted using another way. It is recommended to do this periodically with the Cisco IOS "kron" instead. The following example schedules `wr mem` daily at 0:00.
```
kron policy-list auto_wr_mem
  cli wr mem
  exit

kron occurrence daily_wr_mem at 00:00 recurring
  policy-list auto_wr_mem
  exit

show kron schedule
```

Or using event manager:
```
event manager applet wr_conf
  event timer cron name wr_conf cron-entry " 25 * * * *"
  action 1.0 cli command "enable"
  action 1.1 cli command "wr mem"
  action 1.2 syslog msg "Config has been saved by event manager"
```
