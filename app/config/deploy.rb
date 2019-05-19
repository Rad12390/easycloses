set :application, "Localsbest"
set :domain,      "preview.localsbest.com"
set :deploy_to,   "/home/localsbest/domains/#{domain}/localsbest"
set :app_path,    "app"

set :repository,  "git@bitbucket.org:nimbleimps/localsbest.git"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`
set :dump_assetic_assets, true
set :use_composer, true
set :shared_files,      ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]

set :use_sudo,      false
set :user, "localsbest"
set :writable_dirs,       ["app/cache", "app/logs"]
set :webserver_user,      "#{user}"
set :permission_method,   :chmod
set :use_set_permissions, false


role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set  :keep_releases,  3

# Be more verbose by uncommenting the following line
# logger.level = Logger::MAX_LEVEL