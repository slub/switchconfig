# Switchconfig
Web application for managing Cisco switches via SSH

## Features
- assign description, VLAN and VoIP-capability to a switchport
  - trunk ports are hidden by default to avoid accidentally changes on those ports
- webserver connects to switches via SSH
- create maps with the position of your switches
- overview over the usage of your switch using the port matrix or port list
- mac address search
- bulk password change for one user account on all switches
- optimized for mobile devices
- dark mode!!

## Dependencies
- Server
  - Linux-based operating system (Debian/Ubuntu recommended)
  - Apache2 webserver
  - PHP 7 or 8 with `php-ssh2` package
- Client
  - Chromium-based browser or Firefox
  - JavaScript enabled

## Setup
1. Install packages (example for Debian): `apt install apache2 php php-ssh2`
2. Copy all files into your webserver directory
3. Make sure `AllowOverride All` is set in your Apache config for the webserver directory, in order to deny access to the "maps" directory for non-authenticated users (see `.htaccess` file in this directory).
4. Create/Edit the config file `config.php` (please refer to the explanation and example in the file `config.php.example`)
   - add the vlans you need to the array __VISIBLE_VLAN__
   - add the switches you want to manage to the array __SWITCHES__ (at least one)
   - (optional) set the __VOICE_VLAN__ (integer)
   - (optional) enable the password change feature
   - (optional) create maps using the array __MAPS__
5. Open `index.php` in your browser, log in with an SSH account on your switch and your LDAP account if configured.

## Docker
Docker is currently used/recommended for development/testing purposes only.

Create your config file `config.php` as described above, the execute:
```
docker compose down
docker compose build --no-cache
docker compose up
```

## Hardening Recommendations
- Please only use HTTPS (except you are accessing the site only via localhost). Redirect all HTTP requests to HTTPS.
- Keep your server always up to date.
- Limit the access (via Apache config) to IP addresses that really need it.
- Do not make this webapp available on the internet (to avoid brute force attacks) - configure your Apache and/or firewall to only serve this page inside your internal network.

## Other Recommendations
- LDAP Authentication: You can configure your switches to ask a RADIUS server (which can for example authenticate against an LDAP/AD server) for authenticating the SSH connections.

## Compatibility Note
This application parses the SSH response from your switch. Therefore, your switch has to produce output in a specific format as shown in `docs/Example-SSH-Output.txt` in order to be compatible with this application. Please check if your switch supports the necessary commands (and syntax) in the example file and if it produces similar output.

## Custom Webdesign
To apply a custom (corporate) design, you can create a file `css/custom.css` which will be included in the HTML head automatically. With this, you can e.g. easily change the logo: `#logo { background-image: url('mylogo.png') }`.

## Feedback
I'm interested if your switch model is compatible (or not) with this application. Please let me know on Github (make an Issue) or via email. Thanks!

## Screenshots
![Main Page](img/screenshot/main.png)
![Port List](img/screenshot/list.png)
![Port Matrix](img/screenshot/matrix.png)
![MAC search](img/screenshot/search.png)

## Third-Party Components
- SVG-Loader by SamHerbert, MIT License  
  https://github.com/SamHerbert/SVG-Loaders
- Material Icons, Apache License 2.0  
  https://material.io/tools/icons
- Switch & Key Icons from draw.io  
  https://draw.io

## License
GPL v3, see LICENSE.txt
