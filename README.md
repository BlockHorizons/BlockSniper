# BlockSniper
An innovative new world editing tool, made for PocketMine-MP: A MCPE server software written in PHP.

## Development!
BlockSniper is currently under heavy development. Stable releases will be drafted at [Releases](https://github.com/Sandertv/BlockSniper/releases).
If you do decide to compile a phar file yourself to benefit from the latest features, here's how:
 - Download the .ZIP file with the big green button.
 - Go to pmt.mcpe.me and convert the .ZIP file to a phar file.
 - Upload your phar file to your server and restart the server.

## What is this?
BlockSniper is an advanced world editing tool, based on the idea of VoxelSniper from minecraft PC softwares.
BlockSniper allows you to edit your world on a long range, with several different types. The types are as follows:
> Cuboid    > Spawn a cube in your world
>
> Sphere    > Spawn a sphere in your world
>
> Overlay   > Overlay blocks with a different type
>
> More coming soon!

## How does it work?
BlockSniper has several commands to edit your world. The commands work as follows:
>> /snipe <type> <radius> <block(s)> : Snipe an edit type at the location you're looking at.
>
> Additional blocks can be added by sepparating them with a comma.
>
>
>> /brushwand <type> <radius> <block(s)> : Set a wand with an edit type to shoot everytime you hold your finger on the screen or interact.
>
> Additional blocks can here too be added by sepparating them with a comma.

## Are there permissions? Where can I find them?
A full list of permissions can be found in the plugin.yml file. Here they are:

>>permissions:
>
>    blocksniper:
>
>        default: false
>
>        description: Allows access to all BlockSniper features.
>
>        children:
>
>            blocksniper.command:
>
>                default: false
>
>                description: Allows access to all BlockSniper command features.
>
>                children:
>
>                    blocksniper.command.snipe:
>
>                        default: op
>
>                        description: Allows access to the snipe command.
>
>                    blocksniper.command.brushwand:
>
>                        default: op
>
>                        description: Allows access to the brush wand command.
>
>            blocksniper.shape:
>
>                default: false
>
>                description: Allows access to all BlockSniper shapes.
>
>                children:
>
>                    blocksniper.shape.sphere:
>
>                        default: op
>
>                        description: Allows access to the sphere shape.
>
>                    blocksniper.shape.cuboid:
>
>                        default: op
>
>                        description: Allows access to the cuboid shape.
>
>                    blocksniper.shape.overlay:
>
>                        default: op
>
>                        description: Allows access to the overlay shape.

