# Input Handling

By default, concurrently doesn't send input to any commands it spawns.<br/>
In the below example, typing `rs` to manually restart [nodemon](https://nodemon.io/) does nothing:

```bash
$ concurrently 'nodemon' 'npm run watch-js'
rs
```

To turn on input handling, it's necessary to set the `--handle-input`/`-i` flag.<br/>
This will send `rs` to the first command:

```bash
$ concurrently --handle-input 'nodemon' 'npm run watch-js'
rs
```

To send input to a different command instead, it's possible to prefix the input with the command index, followed by a `:`.<br/>
For example, the below sends `rs` to the second command:

```bash
$ concurrently --handle-input 'npm run watch-js' 'nodemon'
1:rs
```

If the command has a name, it's also possible to target it using that command's name:

```bash
$ concurrently --handle-input --names js,server 'npm run watch-js' 'nodemon'
server:rs
```

It's also possible to change the default command that receives input.<br/>
To do this, set the `--default-input-target` flag to a command's index or name.

```bash
$ concurrently --handle-input --default-input-target 1 'npm run watch-js' 'nodemon'
rs
```
