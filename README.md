# BlockSniper
An innovative new world editing tool, made for PocketMine-MP: An MCPE server software written in PHP.

## Development!
BlockSniper is currently under heavy development. Stable releases will be drafted at [Releases](https://github.com/Sandertv/BlockSniper/releases).
If you do decide to compile a phar file yourself to benefit from the latest features, here's how:
 - Download the .ZIP file with the big green button.
 - Go to pmt.mcpe.me and convert the .ZIP file to a phar file.
 - Upload your phar file to your server and restart the server.

[![Poggit-CI](https://poggit.pmmp.io/ci.badge/Sandertv/BlockSniper/BlockSniper)](https://poggit.pmmp.io/ci/Sandertv/BlockSniper/BlockSniper)

## What is this?
BlockSniper is an advanced world editing tool, based on the idea of VoxelSniper from Mineraft PC softwares.
BlockSniper allows you to edit your world on a long range, with several different types/shapes. The types/shapes are as follows:

>> Clone types
> Template  > Saves the selected clone in a sharable file.
>
> Copy      > Virtually saves the selected area to paste later.
>
>> Shapes
>
> Cube      > As if minecraft wasn't enough cubes already.
>
> Sphere    > Done with trying to build a perfect sphere?
>
> Cylinder (standing) > For the people that really want a unique building.
>
> Cuboid    > Making a cuboid has never been so easy.
>
>> Types
>
> Overlay   > Get those ugly dirt blocks out of sight.
>
> Layer     > Make a thin layer of blocks to fix your terrain.
>
> Replace   > Replace those useless coal blocks for diamond blocks instead!
>
> Leafblower > Blow away all those annoying plants.
>
> Clean     > Restore your terrain from all the ugly dirt houses.
>
> Drain     > Ever wanted to drain an ocean? No?
>
> Flatten   > Finally you don't need a flat world for flat terrain anymore.
>
> More coming soon!

## Translation
BlockSniper has a multi-language system, so everybody can have a good time using the plugin. We currently have nowhere near enough translated languages though, and we need your help! It is very appreciated if you help translate, so please do so whenever you have time. The available languages can be found under resources/languages.

## How does it work?
BlockSniper has several commands to edit your world. The commands work as follows:
>> /snipe <type> <radius> <block(s)> : Snipe an edit type at the location you're looking at.
>
> Additional blocks can be added by separating them with a comma.
>
>> /brushwand <type> <radius> <block(s)> : Set a wand with an edit type to shoot everytime you hold your finger on the screen or interact.
>
> Additional blocks can here too be added by separating them with a comma.
>
>> /undo
>
> Undoes the last modification done.
>
>> /clone <template/copy> <radiusXheight> [name]
>
> Clones the target in either a copy, or a template with the given name.
>
>> /paste <template/copy> [name]
>
> Pastes the current copy, or template with the given name.

## Are there permissions? Where can I find them?
A full list of permissions can be found in the [plugin.yml](https://github.com/Sandertv/BlockSniper/blob/master/plugin.yml) file. 
It contains the most up to date permissions, so every permission available can be found there.

