# BlockSniper<a href="https://github.com/Sandertv/BlockSniper"><img src="https://github.com/Sandertv/BlockSniper/blob/master/resources/BlockSniperLogo.png" width="60" height="60" align="right"></a>

[![Join the chat at https://gitter.im/BlockHorizons/BlockSniper](https://badges.gitter.im/BlockHorizons/BlockSniper.svg)](https://gitter.im/BlockHorizons/BlockSniper?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)<br>
An innovative world editing tool, made for PocketMine-MP: An MCPE server software written in PHP. Worlds can be easily edited using a Golden Carrot, which is used as brush. Find more information about BlockSniper and on how to use it at the [Wiki](https://github.com/Sandertv/BlockSniper/wiki).

> Third party versions, forks or spoons of PocketMine are **not** supported.
> 
> Issues related to other server softwares will be closed immediately.

### Installation
Stable releases will be drafted at [Releases](https://github.com/Sandertv/BlockSniper/releases).
If you do decide to compile a phar file yourself to benefit from the latest features, here's how:
 - Download the .ZIP file with the big green button.
 - Go to pmt.mcpe.me and convert the .ZIP file to a phar file.
 - Upload your phar file to your server and restart the server.

[![Poggit-CI](https://poggit.pmmp.io/ci.badge/Sandertv/BlockSniper/BlockSniper)](https://poggit.pmmp.io/ci/Sandertv/BlockSniper/BlockSniper)

### What is this?
BlockSniper is an advanced world editing tool, based on the idea of VoxelSniper from Mineraft PC softwares.
BlockSniper allows you to edit your world on a long range, with several different types and shapes. Shapes are the shapes types are being executed in. This means that every type can be executed in any shape.
**For more information, make sure to check the [Wiki](https://github.com/Sandertv/BlockSniper/wiki).**

### Translation
BlockSniper has a multi-language system, so everybody can have a good time using the plugin. We currently have nowhere near enough translated languages though, and we need your help! It is very appreciated if you help translate, so please do so whenever you have time. The available languages can be found under resources/languages.

### How does it work?
BlockSniper has several commands to edit your world. The commands can be found on the Wiki page [here](https://github.com/Sandertv/BlockSniper/wiki/Commands).


### Are there permissions? Where can I find them?
A full list of permissions can be found in the [plugin.yml](https://github.com/Sandertv/BlockSniper/blob/master/plugin.yml) file. 
It contains the most up to date permissions, so every permission available can be found there.

### I love/hate BlockSniper! Where can I review this?
BlockSniper can be reviewed at the poggit release page [here](https://poggit.pmmp.io/p/BlockSniper/).
Any feedback given is very welcome, and it would be very much appreciated!

=====

### What does every setting in the settings.yml mean?
The explanation of every setting in the settings.yml can be found below. The [settings.yml file](https://github.com/Sandertv/BlockSniper/blob/master/resources/settings.yml) always contains the latest property explanation.

```
---
# Configuration for BlockSniper: A WorldEdit plugin for PocketMine.

# Internal Property: Do not change.
Configuration-Version: "2.1.0"

# Whether to auto-update configuration when a new version is found.
Auto-Configuration-Update: true

# Language in which messages are displayed. Available languages:
# en (English), nl (Dutch), de (German), fr (French), fa (Persian), ru (Russian), zh_tw (Chinese)
Message-Language: ""

# Item ID of the item that is used to brush. (Golden carrot by default)
Brush-Item: 396

# Maximum radius for shapes/types, it is recommended to keep this number below 20 to prevent server freezes and lag.
Maximum-Radius: 15

# Maximum radius/height for clones, it is recommended to keep this number below 60 to prevent server freezes and lag.
Maximum-Clone-Size: 60

# Whether to spread out the block placement of brush over ticks, or place them all at once. Tick spread brush reduces server lag significantly.
Tick-Spread-Brush: true

# Amount of blocks to place per tick if tick spread brush is enabled.
Blocks-Per-Tick: 200

# Maximum undo and redo stores to save, old ones will get destroyed automatically. Setting this number too high could result in lag or data loss.
Maximum-Undo-Stores: 15

# Whether to reset the size, or make it remain the current size when smallest size with decrement brush is reached.
Reset-Decrement-Brush: true

# Whether to save Brush properties of players after server restart, or dispose them.
Save-Brush-Properties: true

# Whether to drop the plants when using LeafBlower brush, or dispose the items.
Drop-Leafblower-Plants: true
...
```