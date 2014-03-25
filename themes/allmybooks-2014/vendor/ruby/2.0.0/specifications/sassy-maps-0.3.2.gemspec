# -*- encoding: utf-8 -*-

Gem::Specification.new do |s|
  s.name = "sassy-maps"
  s.version = "0.3.2"

  s.required_rubygems_version = Gem::Requirement.new(">= 1.3.6") if s.respond_to? :required_rubygems_version=
  s.authors = ["Sam Richard"]
  s.date = "2014-03-04"
  s.description = "Map helper functions for Sass 3.3 Maps including get-deep and set/set-deep"
  s.email = ["sam@snug.ug"]
  s.homepage = "https://github.com/Snugug/Sassy-Maps"
  s.licenses = ["MIT"]
  s.require_paths = ["lib"]
  s.rubyforge_project = "sassy-maps"
  s.rubygems_version = "2.0.14"
  s.summary = "Map helper functions for Sass 3.3 Maps"

  if s.respond_to? :specification_version then
    s.specification_version = 4

    if Gem::Version.new(Gem::VERSION) >= Gem::Version.new('1.2.0') then
      s.add_runtime_dependency(%q<sass>, ["~> 3.3.0.rc.2"])
      s.add_runtime_dependency(%q<compass>, ["~> 1.0.0.alpha.13"])
    else
      s.add_dependency(%q<sass>, ["~> 3.3.0.rc.2"])
      s.add_dependency(%q<compass>, ["~> 1.0.0.alpha.13"])
    end
  else
    s.add_dependency(%q<sass>, ["~> 3.3.0.rc.2"])
    s.add_dependency(%q<compass>, ["~> 1.0.0.alpha.13"])
  end
end
