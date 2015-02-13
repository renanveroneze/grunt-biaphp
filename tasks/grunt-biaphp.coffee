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

    fs = require 'fs'
    path = require 'path'
    biaphp = require 'biaphp'

    grunt.registerMultiTask 'biaphp', ->

        current = path.dirname __filename
        base = path.join current, '../../../'

        src = path.join base, this.data.cwd
        bin = path.join base, this.data.dest

        files = readdirSyncRecursive src

        for file in files

            final = bin + file.replace(src, '').replace '.bia', '.php'

            if !fs.existsSync path.dirname final

                grunt.util.spawn
                    cmd: 'mkdir'
                    args: ['-p', path.dirname final]

            biaphp file, final






    readdirSyncRecursive = ( dir ) ->

        files = fs.readdirSync dir
        results = []

        for file in files

            if fs.statSync("#{dir}/#{file}").isDirectory()

                results = results.concat readdirSyncRecursive "#{dir}/#{file}"

            else results.push "#{dir}/#{file}"

        return results


