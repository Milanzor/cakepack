<?php

class LaunchShell extends Shell {

    public function main() {

        $descriptorspec = [
            0 => ["pipe", "r"], # StdIn
            1 => ["pipe", "w"], # StdOut
            2 => ["pipe", "w"]  # StdErr
        ];

        $args = [
            'root' => ROOT,
            'webpack-mode' => $this->params['webpack-mode'],
            'build' => $this->params['build'],
            'watch' => $this->params['watch'],
            'vendor-dir' => $this->params['vendor-dir'] ? $this-params['vendor-dir'] : VENDORS,
            'output-dir' => $this->params['output-dir'] ? $this->params['output-dir'] : ROOT . DS . WEBROOT_DIR . 'dist' . DS,
            'entry-root' => $this->params['entry-root'] ? $this->params['entry-root'] : APP . 'View' . DS,
        ];

        $command_args = '';
        foreach ($args as $arg => $val) {
            if ($val === true) {
                $command_args .= ' --' . $arg;
            } elseif ($val !== false) {
                $command_args .= ' --' . $arg . ' ' . $val;
            }
        }

        $bin_path = App::pluginPath('CakePack') . DS . 'bin';
        $process = proc_open('./launch ' . trim($command_args), $descriptorspec, $pipes, $bin_path);

        if (is_resource($process)) {
            while ($s = fgets($pipes[1])) {
                $this->out($s, 0);
            }
        } else {
            $this->out('Something went wrong, sorry, that\'s all I know.');
        }

    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();

        return $parser
            ->addOption('webpack-mode', [
                'short' => 'm',
                'default' => 'production',
                'help' => 'Webpack mode: development|production',
            ])
            ->addOption('vendor-dir', [
                'short' => 'v',
                'default' => VENDORS,
                'help' => 'Provide path to the Vendor directory (should hold your node_modules)',
            ])
            ->addOption('output-dir', [
                'short' => 'o',
                'default' => WEBROOT_DIR . 'dist' . DS,
                'help' => 'Output folder',
            ])
            ->addOption('entry-root', [
                'short' => 'e',
                'default' => APP . 'View' . DS,
                'help' => 'Folder to scan for entry files (will be globbed with **/*.js',
            ])
            ->addOption('build', [
                'short' => 'b',
                'help' => 'Run a one-time build (no watch mode)',
                'boolean' => true,
                'default' => false,
            ])
            ->addOption('watch', [
                'short' => 'w',
                'boolean' => true,
                'help' => 'Run Webpack in watch mode',
                'default' => false,
            ]);
    }
}
