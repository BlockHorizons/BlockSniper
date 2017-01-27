# BlockSniper<a href="https://github.com/Sandertv/BlockSniper"><img src="https://github.com/Sandertv/BlockSniper/blob/master/resources/BlockSniperLogo.png" width="60" height="60" align="right"></a>
An innovative world editing tool, made for PocketMine-MP: An MCPE server software written in PHP. Worlds can be easily edited using a Golden Carrot, which is used as brush.

> Third party versions, forks or spoons of PocketMine are **not** supported.
>
> Issues related to other server softwares will be closed immediately.

## Installation
Stable releases will be drafted at [Releases](https://github.com/Sandertv/BlockSniper/releases).
If you do decide to compile a phar file yourself to benefit from the latest features, here's how:
 - Download the .ZIP file with the big green button.
 - Go to pmt.mcpe.me and convert the .ZIP file to a phar file.
 - Upload your phar file to your server and restart the server.

[![Poggit-CI](https://poggit.pmmp.io/ci.badge/Sandertv/BlockSniper/BlockSniper)](https://poggit.pmmp.io/ci/Sandertv/BlockSniper/BlockSniper)

## What is this?
BlockSniper is an advanced world editing tool, based on the idea of VoxelSniper from Mineraft PC softwares.
BlockSniper allows you to edit your world on a long range, with several different types and shapes. Shapes are the shapes types are being executed in. This means that every type can be executed in any shape.


>> Shapes
>
> Cube      > Selects a cube area around the target block.
>
> Sphere    > Selects a sphere area around the target block.
>
> Cylinder (standing) > Selects a *standing* cylinder are around the target block.
>
> Cuboid    > Selects a cuboid area around the target block.
>
>> Types
>
> Fill      > Basic type, fills up the whole shape with blocks.
>
> Overlay   > Lays a layer of blocks over other blocks.
>
> Layer     > Creates a thin layer of blocks on top of the target block.
>
> Replace   > Replaces a block for other blocks.
>
> Leafblower > Blows away all nearby plants.
>
> Clean     > Cleans up the landscape, only leaving behind ground.
>
> Drain     > Drains all liquid blocks.
>
> Flatten   > Flattens the area around the selected block.
>
> More coming soon!
>
>> Clone types
>
> Template  > Saves the selected clone in a sharable file.
>
> Copy      > Virtually saves the selected area to paste later.

## Translation
BlockSniper has a multi-language system, so everybody can have a good time using the plugin. We currently have nowhere near enough translated languages though, and we need your help! It is very appreciated if you help translate, so please do so whenever you have time. The available languages can be found under resources/languages.

## How does it work?
BlockSniper has several commands to edit your world. The commands work as follows:

>> /brush  < size|shape|type|blocks|height|obsolete >  < args >
>
> Additional blocks can be added by separating them with a comma.
>
>> /undo
>
> Undoes the last modification done.
>
>> /clone  < template/copy >  < radiusXheight >  < name >
>
> Templates can be downloaded and shared, while copies are only temporary.
>
>> /paste  < template/copy >  < name >
>
> Downloaded templates can be pasted using the according name, copies can be pasted without name.

## Are there permissions? Where can I find them?
A full list of permissions can be found in the [plugin.yml](https://github.com/Sandertv/BlockSniper/blob/master/plugin.yml) file. 
It contains the most up to date permissions, so every permission available can be found there.

## I love/hate BlockSniper! Where can I review this?
BlockSniper can be reviewed at the poggit release page [here](https://poggit.pmmp.io/p/BlockSniper/).
Any feedback given is very welcome, and it would be very much appreciated!
