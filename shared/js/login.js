document.writeln('<a href="http://www.wrk.ru/forums/register.php" style="display: block; font-size: 10pt; padding: 2px 4px; text-align: center; box-shadow: 2px 2px 4px rgba(0,0,0,0.5); color: white; background: rgb(28, 184, 65)">Зарегистрироваться</a>')
document.writeln('<form action="/do-login/" method="post"><table>')
document.writeln('<tr><td>Login: <input name="req_username"></td></tr>')
document.writeln('<tr><td>Password: <input name="req_password" type="password"></td></tr>')
document.writeln('<tr><td><input type="submit" value="Login"></td></tr>')
document.writeln('</table></form>')
