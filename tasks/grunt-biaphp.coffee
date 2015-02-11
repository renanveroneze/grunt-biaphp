###

@Grunt
                                ___           ___         ___           ___
     _____                     /\  \         /\  \       /\  \         /\  \
    /::\  \       ___         /::\  \       /::\  \      \:\  \       /::\  \
   /:/\:\  \     /\__\       /:/\:\  \     /:/\:\__\      \:\  \     /:/\:\__\
  /:/ /::\__\   /:/__/      /:/ /::\  \   /:/ /:/  /  ___ /::\  \   /:/ /:/  /
 /:/_/:/\:|__| /::\  \     /:/_/:/\:\__\ /:/_/:/  /  /\  /:/\:\__\ /:/_/:/  /
 \:\/:/ /:/  / \/\:\  \__  \:\/:/  \/__/ \:\/:/  /   \:\/:/  \/__/ \:\/:/  /
  \::/_/:/  /     \:\/\__\  \::/__/       \::/__/     \::/__/       \::/__/
   \:\/:/  /       \::/  /   \:\  \        \:\  \      \:\  \        \:\  \
    \::/  /        /:/  /     \:\__\        \:\__\      \:\__\        \:\__\
     \/__/         \/__/       \/__/         \/__/       \/__/         \/__/
                                                                            @coffee

###

module.exports = ( grunt ) ->

    grunt.registerMultiTask 'biaphp', ->

        #done = @async()

        fs = require 'fs'
        path = require 'path'

        current = path.dirname __filename
        base = path.join current, '../../../'

        src = path.join base, this.data.cwd
        bin = path.join base, this.data.dest

        files = fs.readdirSync src

        for file in files

            final = path.basename( file, '.bia') + '.php'

            grunt.util.spawn
                cmd: 'php'
                args: ['-f', "#{current}/cmdbia.php", "#{src}/#{file}", "#{bin}/#{final}"]

                ->

        #done()


