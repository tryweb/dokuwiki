function isBlank(s)
{
  if ( (s == null) || (s.length == 0) )
    return true;
 
  for(var i = 0; i < s.length; i++)
  {
    var c = s.charAt(i);
	  if ((c != ' ') && (c != '\\n') && (c != '\\t'))
	    return false;
  }
  return true;
}
 
function validatecontact(frm) {
 
  if (isBlank(frm.name.value)) {
    alert ("Please enter a name");
    frm.name.focus();
    return false;
  }
  if (isBlank(frm.email.value) || frm.email.value.indexOf("@") == -1) {
    alert ("Please enter your email address");
    frm.email.focus();
    return false;
  }
 
  if (isBlank(frm.content.value)) {
    alert ("Please add a comment");
    frm.content.focus();
    return false;
	}
}