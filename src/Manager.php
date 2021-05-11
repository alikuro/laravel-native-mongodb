<?php

namespace Alikuro\Mongodb;

use Illuminate\Support\Arr;

// echo extension_loaded("Mongodb") ? "loaded\n" : "not loaded\n";
// echo MONGODB_VERSION, "\n";
class Manager {

    /**
     * Xmongodb
     *
     * An PHP application class for Laravel
     *
     * This content is released under End User License Agreement (EULA)
     *
     * Copyright (c) 2016 - 2017, Muhamad Ali
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
     * THE SOFTWARE.
     *
     * @package CodeIgniter
     * @author  Muhamad Ali
     * @copyright   Copyright (c) 2016 - 2017, End User License Agreement (EULA)
     * @license review
     * @link    http://alikuro.com/application/php/lib/laravel/xmongodb
     * @since   Version 1.0.0 
     * @filesource
     */

    protected $config;
    private $manager;
    private $db = '';
    private $otherdb = '';
    private $collection = '';

    public function __construct($config) {
        $this->config = $config;

        if( !empty( $this->config['options'] ) ) {
            if( trim( $this->config['options']) == "" ) {
                $this->config['options'] = [];
            }
        }

        $this->db = $this->config['database'];
        $dsn = sprintf('mongodb://%s:%d', $this->config['host'], $this->config['port']);
        $this->manager = MongoDriverManager($dsn);
    }

    /**
     * pass reference
     */
    public function get($args='') {

        if($args === 'config') {
            return $this->config;
        }

        if($args === 'manager') {
            return $this->manager;
        }

        if($args === 'db') {
            return $this->db;
        }

        if($args === 'otherdb') {
            return $this->otherdb;
        }

        if($args === 'collection') {
            return $this->collection;
        }

        return null;
    }

}
