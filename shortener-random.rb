#!/usr/bin/env ruby
##
# This file is part of A Local Shortener.
#
# A Local Shortener is free software: you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public License
# as published by the Free Software Foundation, either version 3 of
# the License, or (at your option) any later version.
#
# A Local Shortener is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
# Lesser General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public
# License along with A Local Shortener. If not, see <http://www.gnu.org/licenses/>.
##

require 'securerandom'

# Generates string in base 36
def random_string(length = 4, options = {})
    debug = options[:debug] || false

    min_base36 = '1' + ('0' * (length - 1))
    max_base36 = 'z' * length
    min_base10 = min_base36.to_i(36)
    max_base10 = max_base36.to_i(36)
    r = (SecureRandom.random_number(max_base10 + 1 - min_base10) + min_base10).to_s(36)

    if debug
        c = max_base10 + 1
        c = c.to_s.reverse.gsub(/.{3}/, '\0 ').reverse
        puts "random_string(#{length}, :debug => #{debug})"
        puts "    length: #{length}"
        puts "    interval: [#{min_base36}, #{max_base36}]"
        puts "    combinations: #{c}"
        puts "    result: #{r}"
    end

    return r
end

puts 'Random strings:'
10.times { puts random_string }
