import os


def get_sql(file_name: str) -> str:
    """Get SQL script by its name

    Args:
        file_name (str): SQL script name

    Returns:
        str: script file contents
    """
    with open(os.path.join("/var", "sqlscripts", file_name + ".sql")) as source:
        return source.read()
