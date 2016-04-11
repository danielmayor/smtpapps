## Plugin basado en fluent-plugin-postgres
# https://github.com/uken/fluent-plugin-postgres
# Alimenta base de datos PostgreSQL con mail.log parseado con
# plugin fluent-plugin-filter-parse-postfix
# https://github.com/winebarrel/fluent-plugin-filter-parse-postfix
## Daniel Mayor & Miguel Ángel García - Febrero de 2016

module Fluent
  class Fluent::PostgresOutput < Fluent::BufferedOutput
    Fluent::Plugin.register_output('postgres', self)
    include Fluent::SetTimeKeyMixin
    include Fluent::SetTagKeyMixin

    config_param :host, :string
    config_param :port, :integer, :default => nil
    config_param :database, :string
    config_param :username, :string
    config_param :password, :string, :default => ''

    config_param :key_names, :string, :default => nil # nil allowed for json format
    config_param :sql, :string, :default => nil

    config_param :format, :string, :default => "raw" # or json

    attr_accessor :handler

    def initialize
      super
      require 'pg'
    end

    def configure(conf)
      super
      @key_names = @key_names.split(',')
      @format_proc = Proc.new{|tag, time, record| @key_names.map{|k| record[k]}}
    end

    def start
      super
    end

    def shutdown
      super
    end

    def format(tag, time, record)
      [tag, time, @format_proc.call(tag, time, record)].to_msgpack
    end

    def client
      PG::Connection.new({
        :host => @host, :port => @port,
        :user => @username, :password => @password,
        :dbname => @database
      })
    end

    def write(chunk) # basado ligeramente en el original
      # Abre conexión con PostgreSQL y prepara la
      # sentencia SQL INSERT
      handler = self.client
      handler.prepare("sql_insert", @sql) 
	  
      # recorre el chunk, que contiene los datos de cada línea de log
      chunk.msgpack_each { |tag, time, data|
        # Bloque que cambia el formato de fecha a YYYY-MM-DD
        if data[0][0..4] == "Jan  "
          data[0] = Date.today.strftime("%Y") + "-" + "01" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Jan "
          data[0] = Date.today.strftime("%Y") + "-" + "01" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "Feb  "
          data[0] = Date.today.strftime("%Y") + "-" + "02" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Feb "
          data[0] = Date.today.strftime("%Y") + "-" + "02" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "Mar  "
          data[0] = Date.today.strftime("%Y") + "-" + "03" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Mar "
          data[0] = Date.today.strftime("%Y") + "-" + "03" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "Apr  "
          data[0] = Date.today.strftime("%Y") + "-" + "04" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Apr "
          data[0] = Date.today.strftime("%Y") + "-" + "04" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "May  "
          data[0] = Date.today.strftime("%Y") + "-" + "05" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "May "
          data[0] = Date.today.strftime("%Y") + "-" + "05" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "Jun  "
          data[0] = Date.today.strftime("%Y") + "-" + "06" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Jun "
          data[0] = Date.today.strftime("%Y") + "-" + "06" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "Jul  "
          data[0] = Date.today.strftime("%Y") + "-" + "07" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Jul "
          data[0] = Date.today.strftime("%Y") + "-" + "07" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "Aug  "
          data[0] = Date.today.strftime("%Y") + "-" + "08" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Aug "
          data[0] = Date.today.strftime("%Y") + "-" + "08" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "Sep  "
          data[0] = Date.today.strftime("%Y") + "-" + "09" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Sep "
          data[0] = Date.today.strftime("%Y") + "-" + "09" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "Oct  "
          data[0] = Date.today.strftime("%Y") + "-" + "10" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Oct "
          data[0] = Date.today.strftime("%Y") + "-" + "10" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "Nov  "
          data[0] = Date.today.strftime("%Y") + "-" + "11" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Nov "
          data[0] = Date.today.strftime("%Y") + "-" + "11" + "-" + data[0][4, 11]
        elsif data[0][0..4] == "Dec  "
          data[0] = Date.today.strftime("%Y") + "-" + "12" + "-" + data[0][5, 11]
        elsif data[0][0..3] == "Dec "
          data[0] = Date.today.strftime("%Y") + "-" + "12" + "-" + data[0][4, 11]
        else
          $log.warn "Fecha no válida: #{data[0][0..4]}"
        end
        # va lanzando las consultas
        handler.exec_prepared("sql_insert", data)
      }
      handler.close
    end
  end
end
