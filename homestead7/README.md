# Homestead
Laravel Homestead is an official, pre-packaged Vagrant box that provides you a wonderful development environment without requiring you to install PHP, a web server, and any other server software on your local machine.

This Homestead is customize for Magento development.

## How to use
Change your config in file Homestead.yaml.

Beware a config `networks` with type is `public_network`, your need a unique IP address in your local area network.

You need to create ssh key by excuse a command below
```
ssh-keygen -t rsa -C "youremail@example.com"
```

### Usage
#### Common command
- Start vagrant `vagrant up`
- Restart vagrant `vagrant reload`
- Provision `vagrant provision`. You can use `--provision` when start/restart vagrant
- Stop vagrant `vagrant halt`

#### Vagrant box
- Copy box from server (or ask me 😂)
- Add box `vagrant box add <path/to/the/box.box> --name mkd/homestead`
- List box `vagrant box list`
- Add your Homestead.yaml

```yaml
box: mkd/homestead
```

#### Vagrant plugins
- List plugins `vagrant plugin list`
- Install a new plugin `vagrant plugin install <name-plugin>`. Common plugins: vagrant-bindfs, vagrant-vbguest

#### Advance
To ignore mount a huge folder size, you can config multi folders in your Homestead.yaml file like:

```yaml
folders:
    - map: D:/www/magento1
      to: /home/vagrant/www/magento1
    - map: D:/www/magento2
      to: /home/vagrant/www/magento2
    - map: D:/www/phpmyadmin
      to: /home/vagrant/www/phpmyadmin
```

### Trouble shooting
#### Cannot up Vagrant
If you are using Windows: Turn of firewall. Turn of Anti-virus app.
#### I cannot access by IP address.
 Check your IP address and netmask.
#### Vagrant cannot mount my folders.
Check your path folder which you want to mount. In Windows, you can config: `D:/www` instead of `/d/www`.
Sometime you need to install/update vagrant plugin `vagrant-vbguest`
#### Vagrant is slow down
Install/update vagrant plugin `vagrant-bindfs`


Any issue about this homestead, you can create a new issue. Thank you
