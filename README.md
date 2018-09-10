# BlockSniper<a href="https://github.com/BlockHorizons/BlockSniper"><img src="https://github.com/BlockHorizons/BlockSniper/blob/master/resources/BlockSniperLogo.png" width="60" height="60" align="right"></a>

An innovative brush world editing tool for PocketMine-MP: A Minecraft server software written in PHP.

---

### Overview
BlockSniper is an advanced world editing tool, based on the idea of VoxelSniper from Minecraft PC softwares.
BlockSniper allows you to edit your world on a long range, with several different types and shapes. Shapes are the shapes types are being executed in. This means that every type can be executed in any shape.
A shape would for example be a sphere, a type replace. Every block in that shape will then get modified using the selected type; Replaced.
<br><br>
Almost all features can be managed using an in-game user interface. The brush menu can be opened using the /brush command.
<br><br>
Apart from just brushing, BlockSniper also features things such as copying and pasting (including schematics), undo and redo, presets and an extensive API for other plugins to build on. 
<br>
**For more information, make sure to check the [Wiki](https://github.com/Blockhorizons/BlockSniper/wiki).**

### Installation and Development Builds
Stable releases will be drafted at [Releases](https://github.com/BlockHorizons/BlockSniper/releases) or at the [release page](https://poggit.pmmp.io/p/BlockSniper/) at Poggit.
To install a version:
 - Go to releases from Poggit.
 - Download the attached phar file.
 - Drop the plugin in the plugin folder.
 - Restart the server.

If you do decide to use a development version to benefit from the latest features, the development build list can be found when clicking the button below. (Warning! Only do this if you understand the development versions could contain bugs and be unstable)

[![Poggit-CI](https://poggit.pmmp.io/ci.shield/BlockHorizons/BlockSniper/BlockSniper)](https://poggit.pmmp.io/ci/BlockHorizons/BlockSniper/BlockSniper)

Alternatively, BlockSniper may be downloaded by using:
`git clone --recursive https://github.com/BlockHorizons/BlockSniper`
in the plugin folder if DevTools is installed. Git must be installed.

BlockSniper can _not_ be downloaded by downloading the ZIP file GitHub provides. The required dependencies will not be installed if this is done.

### Translation
BlockSniper has a multi-language system, so everybody can have a good time using the plugin. We currently have nowhere near enough translated languages though, and we need your help! It is very appreciated if you help translate, so please do so whenever you have time. The available languages can be found under resources/languages.

### Permissions
A full list of permissions can be found in the [plugin.yml](https://github.com/BlockHorizons/BlockSniper/blob/master/plugin.yml) file. 
It contains the most up to date permissions, so every permission available can be found there.
Some permissions are also registered dynamically. The permissions of shapes consist out of: `blocksniper.shape.<lowercase shapename>`, whereas the permissions of types consist out of `blocksniper.type.<lowercase typename>`.

### Reviews
BlockSniper can be reviewed at the Poggit release page [here](https://poggit.pmmp.io/p/BlockSniper/).
Any feedback given is very welcome and appreciated. Don't hesitate to share what you think about this plugin.

### Contact
BlockHorizons can be contacted in the Discord server below.
<br><br>

[![Chat](https://img.shields.io/badge/chat-on%20discord-7289da.svg)](https://discord.gg/YynM57V)

<br>

###### Please note that third party modified versions of PocketMine-MP are not supported. Issues caused by them are closed immediately.
