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
